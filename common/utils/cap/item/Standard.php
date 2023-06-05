<?php
namespace common\utils\cap\item;

use common\utils\cap\item\ItemInterface;
use common\models\cap\CapMessages;

use Yii;

class Standard implements ItemInterface
{

    public $xml_data;
    public $object_data;
    public $array_data;

    public $identifier;


    public $references = [];
    public $incidents = [];
    

    public $info = [];


    public function __construct($xml_data)
    {
        $this->xml_data = $xml_data;
        
        $this->object_data = json_decode(json_encode($xml_data));



        $this->array_data = (array) $xml_data;

        $this->setIdentifier();
        $this->setSender();
        $this->setSent();
        $this->setStatus();
        $this->setMsgType();
        $this->setSource();
        $this->setScope();
        $this->setRestriction();
        $this->setRestrictionAddresses();
        $this->setCode();
        $this->setNote();
        $this->setReferences();
        $this->setIncidents();

        if (is_array($this->object_data->info)) {
            $n = 0;
            foreach ($this->object_data->info as $info) {
                $this->setInfo($info, $n);
                $n++;
            }
        } else {
            $this->setInfo($this->object_data->info, 0);
        }
    }

    public function getScheda()
    {
        return $this->identifier;
    }

    public function getSchedaUpdate()
    {
        return null;
    }

    public function getTipoEvento($n)
    {
        return $this->info[$n]['category'];
    }

    public function getSottoTipoEvento($n)
    {
        return null;
    }

    /**
     * Prendi prima referenza
     * operazione sbagliata ma richiesta dal cliente
     * @param  [type] $n [description]
     * @return [type]    [description]
     */
    public function getCallTime($n)
    {
        $ref = CapMessages::find()->where(['in', 'identifier', $this->references])->orderBy(['sent_rome_timezone'=>SORT_ASC])->one();

        return $ref ? $ref->sent_rome_timezone : $this->sent->format('Y-m-d H:i:sP');
    }

    public function getIntTime($n)
    {
        return null;
    }

    public function getArrivalTime($n)
    {
        return null;
    }

    public function getCloseTime($n)
    {
        return null;
    }

    public function getExpiresTime($n)
    {
        if (!isset($this->info[$n]['expires'])) {
            return null;
        }

        return $this->getDateFromFull($this->info[$n]['expires']);
    }


    public function getCodeInt($n)
    {
        return (count($this->incidents) > 0) ? $this->incidents[0] : $this->identifier;
    }

    public function getCodeCall($n)
    {
        return null;
    }

    public function getMajorEvent($n)
    {
        return null;
    }

    public function getFormattedStatus($n)
    {
        return $this->status;
    }

    public function getProfile()
    {
        return 'standard';
    }




    protected function setIdentifier()
    {
        $this->identifier = @$this->object_data->identifier;
    }

    protected function setSender()
    {
        $this->sender = empty((array) $this->object_data->sender) ? '' : $this->object_data->sender;
    }

    protected function setSent()
    {
        $this->sent = new \DateTime(@$this->object_data->sent);
    }

    protected function setStatus()
    {
        $this->status = @$this->object_data->status;
    }

    protected function setMsgType()
    {
        $this->msgType = @$this->object_data->msgType;
    }

    protected function setSource()
    {
        $this->source = (string) @$this->xml_data->source;
    }

    protected function setScope()
    {
        $this->scope = @$this->object_data->scope;
    }

    protected function setRestriction()
    {
        $this->restriction = @$this->object_data->restriction;
    }

    protected function setRestrictionAddresses()
    {
        try {
            $this->addresses = explode(" ", @$this->object_data->addresses);
        } catch (\Exception $e) {
            $this->addresses = [];
        }
    }

    protected function setCode()
    {
        $this->code = @$this->object_data->code;
    }

    protected function setNote()
    {
        $this->note = @$this->object_data->note;
    }

    /**
     * Riferimento, es. evento
     */
    protected function setReferences()
    {
        try {
            $this->references = explode(" ", @$this->object_data->references);
        } catch (\Exception $e) {
            $this->references = [];
        }
    }

    /**
     * Riferimento, es. evento
     */
    protected function setIncidents()
    {
        try {
            $this->incidents = explode(" ", @$this->object_data->incidents);
        } catch (\Exception $e) {
            $this->incidents = [];
        }
    }


    protected function setInfo($info, $n)
    {

        
        foreach ((array) $info as $key => $value) {
            switch ($key) {
                case 'language':
                    $this->info[$n]['language'] = $value;
                    break;
                case 'category':
                    if (!isset($this->info[$n]['categories']) || empty((array) $this->info[$n]['categories'])) {
                        $this->info[$n]['categories'] = [];
                    }

                    $this->info[$n]['categories'][] = $value;
                    break;
                case 'event':
                    $this->info[$n]['event'] = $value;
                    break;
                case 'responseType':
                    if (!isset($this->info[$n]['responseType']) || empty((array) $this->info[$n]['responseType'])) {
                        $this->info[$n]['responseType'] = [];
                    }
                    
                    $this->info[$n]['responseTypes'][] = $value;
                    break;
                case 'urgency':
                    $this->info[$n]['urgency'] = $value;
                    break;
                case 'severity':
                    $this->info[$n]['severity'] = $value;
                    break;
                case 'certainty':
                    $this->info[$n]['certainty'] = $value == 'Very Likely' ? 'Likely' : $value;
                    break;
                case 'audience':
                    $this->info[$n]['audience'] = $value;
                    break;
                case 'senderName':
                    $this->info[$n]['senderName'] = $value;
                    break;
                case 'headline':
                    $this->info[$n]['headline'] = $value;
                    break;
                case 'description':
                    $this->info[$n]['description'] = $value;
                    break;
                case 'instruction':
                    $this->info[$n]['instruction'] = empty((array) $value) ? '' : $value;
                    break;
                case 'web':
                    $this->info[$n]['web'] = empty((array) $value) ? '' : $value;
                    break;
                case 'contact':
                    $this->info[$n]['contact'] = empty((array) $value) ? '' : $value;
                    break;
                case 'eventCode':
                    if (!isset($this->info[$n]['eventCodes'])) {
                        $this->info[$n]['eventCodes'] = [];
                    }
                    if (is_array($value)) {
                        foreach ($value as $v) {
                            $this->info[$n]['eventCodes'][$v->valueName] = $v->value;
                        }
                    } else {
                        $this->info[$n]['eventCodes'][$value->valueName] = $value->value;
                    }
                    break;
                case 'parameter':
                    if (!isset($this->info[$n]['parameters'])) {
                        $this->info[$n]['parameters'] = [];
                    }

                    if (is_array($value)) {
                        foreach ($value as $v) {
                            $this->info[$n]['parameters'][$v->valueName] = $v->value;
                        }
                    } else {
                        $this->info[$n]['parameters'][$value->valueName] = $value->value;
                    }
                    break;
                case 'effective':
                case 'onset':
                case 'expire':
                case 'expires':
                    if (is_string($value)) {
                        $this->info[$n][$key] = new \DateTime($value);
                    }
                    break;

                case 'resource':
                    if (!isset($this->info[$n]['resource'])) {
                        $this->info[$n]['resource'] = [];
                    }

                    $value = (array) $value;
                    $this->info[$n]['resource'][] = [
                        'resourceDesc' => empty((array) @$value['resourceDesc']) ? '' : @$value['resourceDesc'],
                        'mimeType' => empty((array) @$value['mimeType']) ? '' : @$value['mimeType'],
                        'size' => empty((array) @$value['size']) ? '' : @$value['size'],
                        'uri' => empty((array) @$value['uri']) ? '' : @$value['uri'],
                        'derefUri'=> empty((array) @$value['derefUri']) ? '' : @$value['derefUri'],
                        'digest' => empty((array) @$value['digest']) ? '' : @$value['digest']
                    ];
                    break;
            }
        }

        if (!isset($this->info[$n]['language'])) {
            $this->info[$n]['language'] = 'en-US';
        }

        $this->setPosition($info, $n);
    }

    protected function setPosition($info, $n)
    {
        $area = (array) $info->area;
        foreach ($area as $key => $value) {
            switch ($key) {
                case 'circle':
                    $this->setCircle($n, $value);
                    break;
                case 'polygon':
                    $this->setPolygon($n, $value);
                    break;
                case 'areaDesc':
                    $this->info[$n]['address'] = $value;
                    break;
                case 'geocode':
                    if (!isset($this->info[$n]['geocodes'])) {
                        $this->info[$n]['geocodes'] = [];
                    }

                    $value = (array) $value;
                    $this->info[$n]['geocodes'][$value['valueName']] = $value['value'];
                    break;
            }
        }

        $this->setGeometry($n);
        $this->setCentroid($n);
    }

    protected function setGeometry($i)
    {
        $n = count(@$this->info[$i]['geometries']);
        if ($n == 0) {
            return;
        }

        if ($n > 1) {
            $q = "SELECT ST_GeomFromText( ST_AsText( ST_Collect('" . implode("'::geometry,'", $this->info[$i]['geometries']) . "'::geometry))) as union;";
            $this->info[$i]['geometry'] = Yii::$app->db->createCommand($q)->queryAll()[0]['union'];
            return;
        }

        if ($n == 1) {
            $this->info[$i]['geometry'] = $this->info[$i]['geometries'][0];
        }
    }

    protected function setCircle($n, $value)
    {

        $split = explode(" ", $value);
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
        
        $this->info[$n]['geometries'][] = $q[0]['g'];
    }

    protected function setPolygon($n, $value)
    {
        $split = explode(" ", $value);

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
        
        $this->info[$n]['geometries'][] = Yii::$app->db->createCommand($q, [':string'=>$str])->queryAll()[0]['g'];
    }

    protected function setCentroid($n)
    {
        if (empty($this->info[$n]['geometry']) || $this->info[$n]['geometry'] == '') {
            return;
        }

        $centroid = Yii::$app->db->createCommand("SELECT ST_Centroid( '".$this->info[$n]['geometry']."'::geometry ) as centroid")->queryAll()[0]['centroid'];

        $coords = Yii::$app->db->createCommand("SELECT 
            ST_X( '".$centroid."'::geometry ) as lon,
            ST_Y( '".$centroid."'::geometry ) as lat")->queryAll();

        $this->info[$n]['lat'] = $coords[0]['lat'];
        $this->info[$n]['lon'] = $coords[0]['lon'];
        $this->info[$n]['center_geometry'] = $centroid;
    }


    public function __toString()
    {
    }

    private function getDateFromFull($date)
    {
        
        $d = \DateTime::createFromFormat("Y-m-d?H:i:sP", $date);
        if (is_bool($d)) {
            return null;
        }

        return $d->format('Y-m-d H:i:sP');
    }
}
