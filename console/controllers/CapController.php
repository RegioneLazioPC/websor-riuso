<?php
namespace console\controllers;

use common\models\cap\CapMessages;
use common\models\cap\CapResources;

use Exception;
use Yii;
use yii\console\Controller;

use common\utils\cap\CapFeed;

class CapController extends Controller
{

    private $url = 'https://capitem.vigilfuoco.it/capserver/atom/com.roma@cert.vigilfuoco.it/agenziaprotezionecivile@regione.lazio.it';

    private $_url = 'https://capitem.vigilfuoco.it/capserver/rss/com.roma@cert.vigilfuoco.it/agenziaprotezionecivile@regione.lazio.it';

    /**
     * Script per parsing feed da chronjob
     * @return [type] [description]
     */
    public function actionParse()
    {

        $feed = CapResources::find()->where(['locked'=>0,'removed'=>0])->orderBy(['id'=>SORT_ASC])->all();

        foreach ($feed as $risorsa) {
            $token = sem_get($risorsa->getSemaphore(), 1);

            $acquired = sem_acquire($token, true);
            if (!$acquired) {
                continue;
            }
            
            try {
                $last_messages = CapMessages::find()->where(['id_resource'=>$risorsa->id])->orderBy(['sent'=>SORT_DESC])->limit(100)->all();
                $to_exclude = array_map(function ($message) {
                    return $message->url;
                }, $last_messages);

                $f = new CapFeed($risorsa);
                $f->excludeUrls($to_exclude);
                $f->loadItems();

                $n = 0;
                $f->parseItems(function ($i) use (&$n, $risorsa) {
                   
                    CapMessages::buildFromResource($i, $risorsa);

                    $n++;
                });

                $risorsa->last_check = time();
                if (!$risorsa->save()) {
                    throw new \Exception(json_encode($risorsa->getErrors()), 1);
                }

                sem_release($token);
            } catch (\Exception $e) {
                sem_release($token);
                Yii::error($e, 'cap');
            }
        }
    }

    /**
     * Script parsing feed cap long running
     * @return [type] [description]
     */
    public function actionParseLongRunning()
    {

        while (1) {
            $feed = CapResources::find()->where(['locked'=>0,'removed'=>0])->orderBy(['id'=>SORT_ASC])->all();

            foreach ($feed as $risorsa) {
                $token = sem_get($risorsa->getSemaphore(), 1);

                $acquired = sem_acquire($token, true);
                if (!$acquired) {
                    continue;
                }

                try {
                    $last_messages = CapMessages::find()->where(['id_resource'=>$risorsa->id])->orderBy(['sent'=>SORT_DESC])->limit(100)->all();
                    $to_exclude = array_map(function ($message) {
                        return $message->url;
                    }, $last_messages);

                    $f = new CapFeed($risorsa);
                    $f->excludeUrls($to_exclude);
                    $f->loadItems();

                    $n = 0;
                    $f->parseItems(function ($i) use (&$n, $risorsa) {
                        
                        CapMessages::buildFromResource($i, $risorsa);

                        $n++;
                    });

                    $risorsa->last_check = time();
                    if (!$risorsa->save()) {
                        throw new \Exception(json_encode($risorsa->getErrors()), 1);
                    }

                    sem_release($token);
                } catch (\Exception $e) {
                    sem_release($token);
                    Yii::error($e, 'cap');
                }
            }

            sleep(30);
        }
    }


    public function actionUpdate()
    {
      
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $this->url, [
            'auth' => [Yii::$app->params['cap_test_username'], Yii::$app->params['cap_test_password']]
        ]);

        $status = $res->getStatusCode();
        if ($status != 200) {
            echo "Stato $status\n";
            return;
        }

        $m_ids = CapMessages::find()->all();
        $urls = array_map(
            function ($cap) {
                return $cap->url;
            },
            $m_ids
        );

        $xml = new \SimpleXMLElement($res->getBody());

        // ok per rss
        //$items = $xml->xpath('//item');

        $encoded = json_decode(json_encode($xml), true);
        $items = $encoded['entry'];

        foreach ($items as $item) {
            echo "ITEM\n";
            
            // feed atom
            if (gettype($item['link']) == 'array') {
                $link = "";
                foreach ($item['link'] as $v) {
                    if (in_array($v['@attributes']['rel'], ['edit-media', 'alternate', 'enclosure'])) {
                        $link = $v['@attributes']['href'];
                        echo $link . "\n";
                        continue;
                    }
                }

            // feed rss
            } else {
                $link = $item->link;
            }

            if (in_array($link, $urls)) {
                echo $link . " già visionato \n";
                continue;
            }

            echo "Chiamo " . gettype($link) . " " . $link . "\n";

            $content = new \GuzzleHttp\Client();
            $res = $client->request('GET', (string) $link, [
                'auth' => [Yii::$app->params['cap_test_username'], Yii::$app->params['cap_test_password']]
            ]);

            if ($status == 200) {
                $plain_content = $res->getBody();
                //$xml = new \SimpleXMLElement( $res->getBody() );
                $x = preg_replace("/<contact>(.*)<\/contact>/", "<contact></contact>", $plain_content);
                
                $xml = new \SimpleXMLElement($x);
                $json_content = (array) $xml;



                echo $xml->references . "\n";
                if ($xml->references) {
                    $both = explode(" ", $xml->references);

                    $ref = [];
                    foreach ($both as $referral) {
                        $str = explode(",", $referral);
                        if (isset($str[1])) {
                            $ref[] = $str[1]; // prendo quello al centro
                        }
                    }
                } else {
                    $ref = [$xml->identifier];
                }
                

                foreach ($ref as $riferimento) {
                    $new_cap = new CapMessages;
                    $new_cap->cap_feed_url = $this->url;
                    $new_cap->url = $link;
                    $new_cap->identifier = $xml->identifier;
                    $new_cap->ref_identifier = $riferimento;
                    $new_cap->type = $xml->msgType;
                    $new_cap->xml_content = $x;
                    $new_cap->json_content = $json_content;

                    // area
                    $area = (array) $json_content['info']->area;

                    $geoms = [];

                    foreach ($area as $key => $value) {
                        switch ($key) {
                            case 'circle':
                                $split = explode(" ", $value);
                                $coords = explode(",", $split[0]);
                                $q = Yii::$app->db->createCommand("SELECT 
                                    ST_Buffer(
                                        ST_SetSRID( ST_MakePoint((:lat)::float,(:lon)::float), 4326),
                                        :radius
                                    ) as g", [
                                        ':lat' => (float) $coords[0],
                                        ':lon' => (float) $coords[1],
                                        ':radius' => (float) $split[1]
                                    ])->queryAll();
                                $geoms[] = $q[0]['g'];
                                break;
                            case 'polygon':
                                $split = explode(" ", $value);
                                break;
                        }
                    }


                    //if(!$new_cap->save()) var_dump($new_cap->getErrors());
                }

                echo "Inserito " . $xml->identifier . "\n";
            }

            sleep(1);
        }
    }


    public function actionTestCircle()
    {
        $string_1 = "41.77949280,12.34983560 0.10";
        //$string_2 = "41.78949280,12.34983560 0.30";

        $split = explode(" ", $string_1);
        $coords = explode(",", $split[0]);
        $q = Yii::$app->db->createCommand("SELECT 
            ST_Buffer(
                ST_SetSRID( ST_MakePoint((:lon)::float,(:lat)::float), 4326),
                :radius
            ) as g", [
                ':lat' => (float) $coords[0],
                ':lon' => (float) $coords[1],
                ':radius' => (float) $split[1]
            ])->queryAll();
        $geoms[] = $q[0]['g'];

        $split = explode(" ", $string_2);
        $coords = explode(",", $split[0]);
        $q = Yii::$app->db->createCommand("SELECT 
            ST_Buffer(
                ST_SetSRID( ST_MakePoint((:lon)::float,(:lat)::float), 4326),
                :radius
            ) as g", [
                ':lat' => (float) $coords[0],
                ':lon' => (float) $coords[1],
                ':radius' => (float) $split[1]
            ])->queryAll();
        $geoms[] = $q[0]['g'];


        $other_q = "SELECT ST_GeomFromText( ST_AsText( ST_Collect('" . implode("'::geometry,'", $geoms) . "'::geometry))) as union;";

        $union = Yii::$app->db->createCommand($other_q)->queryAll()[0]['union'];


        $centroid = Yii::$app->db->createCommand("SELECT ST_Centroid( '".$union."'::geometry ) as centroid")->queryAll()[0]['centroid'];


        $coords = Yii::$app->db->createCommand("SELECT 
            ST_X( '".$centroid."'::geometry ) as lon,
            ST_Y( '".$centroid."'::geometry ) as lat")->queryAll();

        echo "UNION:" . $union . "\n";
        echo "CENTROID:" . $centroid . "\n";
        echo "LAT LON:" . $coords[0]['lat'] . " " . $coords[0]['lon'] . "\n";
    }


    

    public function actionTestPolygon()
    {
        $poly_string = "41.9658228,12.3988554 41.9658167,12.4042143 41.9539069,12.4098675 41.9341747,12.3636007";

        $split = explode(" ", $poly_string);

        $q = "SELECT 
            ST_MakePolygon ( ST_GeomFromText(:string) ) as g;";
        $str = 'LINESTRING(';
        
        $n = 1;
        $n_split = 0;
        $first = "";
        foreach ($split as $coords) {
            $c = explode(",", $coords);
            $str .= $c[1] . " " . $c[0];

            if ($n_split == 0) {
                $first .= $c[1] . " " . $c[0];
            }

            $str .= ",";

            $n_split++;
        }
        $str .= $first;
        $str .= ')';
        
        $geom = [];

        // creo un multipoligono
        $geom[] = Yii::$app->db->createCommand($q, [':string'=>$str])->queryAll()[0]['g'];
        $geom[] = $this->otherPolygon();

        $other_q = "SELECT ST_GeomFromText( ST_AsText( ST_Collect('" . implode("'::geometry,'", $geom) . "'::geometry))) as union;";
        $union = Yii::$app->db->createCommand($other_q)->queryAll()[0]['union'];

        $centroid = Yii::$app->db->createCommand("SELECT ST_Centroid( '".$union."'::geometry ) as centroid")->queryAll()[0]['centroid'];


        $coords = Yii::$app->db->createCommand("SELECT 
            ST_X( '".$centroid."'::geometry ) as lon,
            ST_Y( '".$centroid."'::geometry ) as lat")->queryAll();

        echo "UNION:" . $union . "\n";
        echo "CENTROID:" . $centroid . "\n";
        echo "LAT LON:" . $coords[0]['lat'] . " " . $coords[0]['lon'] . "\n";
    }

    public function otherPolygon()
    {
        $poly_string = "42.9658228,12.3988554 41.9658167,12.4042143 41.9539069,12.4098675 42.9341747,12.3636007";

        $split = explode(" ", $poly_string);

        $q = "SELECT 
            ST_MakePolygon ( ST_GeomFromText(:string) ) as g;";
        $str = 'LINESTRING(';
        
        $n = 1;
        $n_split = 0;
        $first = "";
        foreach ($split as $coords) {
            $c = explode(",", $coords);
            $str .= $c[1] . " " . $c[0];

            if ($n_split == 0) {
                $first .= $c[1] . " " . $c[0];
            }

            $str .= ",";

            $n_split++;
        }
        $str .= $first;
        $str .= ')';
        
        return Yii::$app->db->createCommand($q, [':string'=>$str])->queryAll()[0]['g'];
    }
}
