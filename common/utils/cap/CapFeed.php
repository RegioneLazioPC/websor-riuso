<?php

namespace common\utils\cap;

use Yii;
use common\utils\cap\item\Base;
use common\utils\cap\item\Standard;
use common\utils\cap\item\Vvf;

class CapFeed
{

    public $url;
    public $profile;
    public $preferred_feed;
    public $items = [];
    public $items_collection = [];

    // url dei singoli item da non richiamare
    protected $excluded = [];

    protected $auth;
    protected $guzzle_options = [
        'base_uri' => 'http://websorapi/cap/rss'
    ];
    protected $username;
    protected $password;


    protected $risorsa;

    public $xml_data;

    /**
     * Risorse CAP
     * @param \common\models\cap\CapResources $risorsa [description]
     */
    public function __construct(\common\models\cap\CapResources $risorsa)
    {
        $this->url = $risorsa->preferred_feed == 'rss' ? $risorsa->url_feed_rss : $risorsa->url_feed_atom;
        $this->profile = $risorsa->profile;
        $this->preferred_feed = $risorsa->preferred_feed;
        $this->risorsa = $risorsa;

        $this->auth = $risorsa->autenticazione;

        $this->username = $risorsa->username;
        $this->password = !empty($risorsa->password) ? Yii::$app->getSecurity()->decryptByPassword(
            base64_decode($risorsa->password),
            Yii::$app->params['cap_password_secret_key']
        ) : null;

        if (isset(Yii::$app->params['laziocreaserver']) && isset(Yii::$app->params['proxyUrl'])) :
            $this->guzzle_options['proxy'] = Yii::$app->params['proxyUrl'];
        endif;
    }

    public function excludeUrls(array $array)
    {
        $this->excluded = $array;
    }

    /**
     * Carica i singoli messaggi
     * @return [type] [description]
     */
    public function loadItems()
    {

        try {
            $opt = array_merge($this->guzzle_options, [
                'base_uri' => $this->url
            ]);
            
            $client = new \GuzzleHttp\Client($opt);
            
            $res = $client->request('GET', $this->url, $this->getHeadersCall());

            $status = $res->getStatusCode();
            if ($status != 200) {
                throw new \Exception("Errore chiamata cap feed url " . $this->url, 1);
            }

            $this->xml_data = new \SimpleXMLElement($res->getBody());
            $this->setItems();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Inizializza ogni item
     * se stanno tra gli url da escludere non li chiamare
     *
     * @return [type] [description]
     */
    public function parseItems($cb = null)
    {

        //$i = 0;


        foreach ($this->items as $item) {
            //$i++;
            //echo "Parsato ".$i."\n\n";
            
            $link = $this->getItemLink($item);
            if (in_array($link, $this->excluded)) {
                continue;
            }

            // usando la callback si dimezza il peso e diventa circa 20kb per ogni record
            // ogni record mi richiede circa 50kb
            /*$this->items_collection[]*/ $item_formatted = new \common\utils\cap\item\Base(
                $this->getItemLink($item),
                $this->profile,
                $this->getHeadersCall(),
                $this->guzzle_options
            );

            if ($cb) {
                $cb($item_formatted);
            }

            //echo $this->convertMemory(memory_get_usage()) . "\n";

            unset($item_formatted);
        }
    }

    protected function convertMemory($size)
    {
        
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024, ($i=floor(log($size, 1024)))), 2).' '.$unit[$i];
    }

    /**
     * Prendi link di un item
     * @param  [type] $item [description]
     * @return [type]       [description]
     */
    protected function getItemLink($item)
    {
        if (gettype($item['link']) == 'array') {
            $link = "";
            foreach ($item['link'] as $v) {
                if (in_array($v['@attributes']['rel'], ['edit-media', 'alternate', 'enclosure'])) {
                    return $v['@attributes']['href'];
                }
            }

        // feed rss
        } else {
            return $item->link;
        }

        return null;
    }

    /**
     * Imposta gli item
     */
    protected function setItems()
    {
        if (strtolower($this->preferred_feed) == 'rss') {
            $this->setRssItems();
        } else {
            $this->setAtomItems();
        }

        // inverto l'ordine per avere a disposizione prima i più vecchi,
        // in questo modo quando va a prendere le references dovrebbe averle già salvate
        $this->items = array_reverse($this->items);
    }

    /**
     * Imposta gli item dal feed atom
     */
    protected function setAtomItems()
    {
        $encoded = json_decode(json_encode($this->xml_data), true);
        $this->items = $encoded['entry'];
    }

    /**
     * impost gli item con il feed rss
     */
    protected function setRssItems()
    {
        $this->items = $this->xml_data->xpath('//item');
    }

    /**
     * Header chiamata http
     * @return [type] [description]
     */
    public function getHeadersCall()
    {
        if ($this->auth == 'basic') {
            return $this->basicAuthHeaders();
        }

        return [
            'User-Agent' => Yii::$app->params['cap_call_user_agent'],
        ];
    }

    /**
     * header basic auth
     * @return [type] [description]
     */
    protected function basicAuthHeaders()
    {
        return [
            'auth' => [$this->username, $this->password],
            'User-Agent' => Yii::$app->params['cap_call_user_agent'],
        ];
    }
}
