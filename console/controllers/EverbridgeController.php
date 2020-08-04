<?php
namespace console\controllers;

use Exception;
use Yii;
use yii\console\Controller;
use common\utils\EverbridgeUtility;

use common\models\ente\EntEnte;
use common\models\utility\UtlContatto;

class EverbridgeController extends Controller
{
    
    
    private $max = 10;

    /**
     * Cicla i contatti memory safe
     * @param  [array] $contacts 
     * @return [array]
     */
    private function ciclateContacts( $contacts ) 
    {
        foreach ($contacts as $contact) {
            $c_c =  \common\models\ViewRubrica::find()->where(['identificativo'=>$contact['identificativo']])->all();
            yield EverbridgeUtility::formatDestinatario ( $c_c );
        }
    }

    /**
     * ./yii everbridge/test
     * @deprecated
     */
    public function actionTest() {
        $duplicati = \common\models\MasSingleSend::find()->select(['valore_rubrica_contatto'])
            ->where('id_feedback is null')
            ->andWhere(['id_invio'=>279])
            ->andWhere(['status'=>\common\models\MasSingleSend::STATUS_DUPLICATED])
            ->asArray()
            ->all();
        $d = [];
        foreach ($duplicati as $duplex) {
            $d[] = $duplex['valore_rubrica_contatto'];
        }
        var_dump($d);

    }

    /**
     * Sincronizza i contatti della rubrica con Everbridge
     * 
     * ./yii everbridge/sync
     * 
     * @return void
     */
    public function actionSync() {
                
        $records = \common\models\ViewRubrica::find()
        ->from(['t' => '(SELECT distinct on (identificativo) * FROM view_rubrica)'])
        ->asArray()
        ->all();

        echo count($records) . "\n";
        $n = 0;
        $recs = [];
        foreach ( $this->ciclateContacts( $records ) as $record) {
            echo "prendo record ciclato\n";
            //echo json_encode($record);
            $recs = array_merge( $recs, $record );
            $n += count($record);

            if ( $n >= $this->max ) {
                echo "Inserisco su everbridge\n";
                $this->addSingle( $recs );

                $recs = [];
                $n = 0;
                sleep(2);
            }
            
        }

        echo count($recs);
        if(count($recs) > 0) {
            $this->addSingle( $recs );
            $recs = [];
            $n = 0;
        }

    }

    /**
     * Aggiunge dati di test su Everbridge
     * @deprecated
     * 
     * ./yii everbridge/add-test-data
     * @return void
     */
    public function actionAddTestData() {
        $base_n = 0;
        for($x = 0; $x < 200; $x++) {
            $e = new EntEnte;
            $e->denominazione = 'Test';
            $e->save();

            $rand = rand(1,5);
            for($n = 0; $n <= $rand; $n++){
                $base_n++;
                $cont = new UtlContatto;
                $cont->contatto = 'test' . $base_n . '@mailinator.com';
                $cont->type = 0;
                $cont->check_predefinito = 1;
                $cont->save();
                $e->link('contatto', $cont, ['use_type'=>2]);
            }
        }   
    }

    /**
     * ./yii everbridge/delete-test-data
     * @return void
     */
    public function actionDeleteTestData() {
        Yii::$app->db->createCommand("DELETE FROM ent_ente WHERE denominazione = 'Test'")->execute();
    }

    /**
     * Elimina i contatti da Everbridge inviando un csv con una singola riga facendo il replace
     * 
     * Su Everbridge il processo è asincrono, è meglio aspettare un paio di minuti prima di fare ulteriori modifiche su everbridge
     * 
     * ./yii everbridge/empty-everbridge
     * @return void
     */
    public function actionEmptyEverbridge() {
        $this->makeCsv();
    }

    /**
     * Ripulisci l'aggregazione con gli ext_ids
     * Elimina i record di con_view_rubrica_everbridge_ext_ids che non hanno corrispondenza nella vista view_rubrica
     * 
     * ./yii everbridge/clean-ext-ids
     * 
     * @return void
     */
    public function actionCleanExtIds() {
        Yii::$app->db->createCommand("DELETE FROM con_view_rubrica_everbridge_ext_ids c WHERE (SELECT count(*) FROM view_rubrica v WHERE v.ext_id = c.ext_id) = 0")->execute();
    }

    /**
     * Invia un csv vuoto a Everbridge
     * @return void
     */
    private function makeCsv() {
        
        $temp = fopen( __DIR__ . '/../data/everbridge_temp/' . time() . '_contatti.csv','w');
        
        $out = "First Name,Middle Initial,Last Name,Suffix,External ID,Country,Business Name,Record Type,FAX 1,FAX Country 1,SMS 1,SMS 1 Country,SMS 2,SMS 2 Country,Email Address 1,Email Address 1\r\n";


        $out .= 'Fabio,,Test,,99999999999,IT,,Employee,,,+393334455666,IT,+393344455666,IT,fabio@mailinator.com,fabio.@mailinator.com';

        fwrite($temp, $out);

        $endpoint = 'https://api.everbridge.net/rest/uploads/'.Yii::$app->params['everbridge']['EVERBRIDGE_ORGANIZATION_ID'];

        try {

        
            $caller = new \GuzzleHttp\Client( [] );
            $response = $caller->request('POST', $endpoint, [
                        'auth' => [
                            Yii::$app->params['everbridge']['EVERBRIDGE_USER'], 
                            Yii::$app->params['everbridge']['EVERBRIDGE_PASSWORD']
                        ],
                        'multipart' => [
                            [
                                'name'     => 'file',
                                'contents' => fopen( stream_get_meta_data($temp)['uri'] , 'r' )
                            ]
                        ],
                    ]);

            var_dump( $response->getBody()->getContents() );


        } catch( \Exception $e ) {
            var_dump( $e->getResponse() );
        }

       

    }

    /**
     * Invia un singolo elemento a Everbridge
     * @param void
     */
    private function addSingle( $data ) {
        $endpoint = 'https://api.everbridge.net/rest/contacts/'.Yii::$app->params['everbridge']['EVERBRIDGE_ORGANIZATION_ID'].'/batch';

        try {

            //echo json_encode( $data/*$record_to_add*/ );
            $opt = (isset(Yii::$app->params['laziocreaserver']) && isset(Yii::$app->params['proxyUrl'])) ?
            ['proxy' => Yii::$app->params['proxyUrl'] ]
            : [];
            
            $caller = new \GuzzleHttp\Client( $opt );
            $response = $caller->request('POST', $endpoint, [
                        'auth' => [
                            Yii::$app->params['everbridge']['EVERBRIDGE_USER'], 
                            Yii::$app->params['everbridge']['EVERBRIDGE_PASSWORD']
                        ],
                        'json' => $data,//$record_to_add,
                    ]);

            

            var_dump( $response->getBody()->getContents() );



        } catch( \Exception $e ) {
            //echo $e->getResponse()->getBody()->getContents();
            echo $e->getMessage();
        }
    }

    

}