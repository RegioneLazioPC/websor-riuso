<?php

namespace common\models\reportistica;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UtlIngaggioSearch represents the model behind the search form of `common\models\UtlIngaggio`.
 */
class ViewReportAttivazioni extends \yii\db\ActiveRecord
{
    public $data_dal, $data_al, $aggregatore;
    public static function tableName()
    {
        return 'view_report_attivazioni';
    }

    public function attributes() {
        return [
            'data_dal',
            'data_al',
            'aggregatore',
            'aggregatore_automezzi',
            'aggregatore_attrezzature',
            'id_attivazione',
            'id_evento',
            'created_at',
            'closed_at',
            'durata',
            'mese',
            'anno',
            'num_protocollo',
            'tipologia',
            'sottotipologia',
            'gestore',
            'id_gestore',
            'coc',
            'indirizzo',
            'id_comune',
            'comune',
            'id_provincia',
            'provincia',
            'provincia_sigla',
            'id_automezzo',
            'targa',
            'id_tipo_automezzo',
            'tipo_automezzo',
            'id_attrezzatura',
            'id_tipo_attrezzatura',
            'modello_attrezzatura',
            'tipo_attrezzatura',
            'num_elenco_territoriale',
            'id_organizzazione',
            'organizzazione',
            'indirizzo_sede',
            'tipo_sede',
            'stato',
            'motivazione_rifiuto',
            'note',
            'lat',
            'lon',
            'geom'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'id_attivazione',
                    'id_evento',
                    'aggregatore_automezzi',
                    'aggregatore_attrezzature',
                    'aggregatore',
                    'created_at',
                    'closed_at',
                    'durata',
                    'mese',
                    'anno',
                    'num_protocollo',
                    'tipologia',
                    'sottotipologia',
                    'gestore',
                    'id_gestore',
                    'coc',
                    'indirizzo',
                    'id_comune',
                    'comune',
                    'id_provincia',
                    'provincia',
                    'provincia_sigla',
                    'id_automezzo',
                    'targa',
                    'id_tipo_automezzo',
                    'tipo_automezzo',
                    'id_attrezzatura',
                    'id_tipo_attrezzatura',
                    'tipo_attrezzatura',
                    'modello_attrezzatura',
                    'num_elenco_territoriale',
                    'id_organizzazione',
                    'organizzazione',
                    'indirizzo_sede',
                    'tipo_sede',
                    'stato',
                    'motivazione_rifiuto',
                    'note',
                    'lat',
                    'lon',
                    'geom'
                ], 'safe'],
                [[
                    'data_dal',
                    'data_al'
                ], 'date', 'format'=>'d-m-Y']
        ];
    }

    public static function getStati() 
    {
        return [
            'In attesa di conferma' => 'In attesa di conferma',
            'Confermato' => 'Confermato',
            'Rifiutato' => 'Rifiutato',
            'Chiuso' => 'Chiuso',
        ];
    }

    
    /**
     * 
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        
        
        $query = ViewReportAttivazioni::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        

        $query->andFilterWhere([
            'id_attivazione' => $this->id_attivazione,
            'id_evento' => $this->id_evento,
            'id_organizzazione' => $this->id_organizzazione,
            'id_automezzo' => $this->id_automezzo,
            'id_attrezzatura' => $this->id_attrezzatura,
            'stato' => $this->stato
        ]);

        if($this->created_at !=""){ 
            $query->andFilterWhere(['>=', 'created_at', Yii::$app->formatter->asDate($this->created_at, 'php:Y-m-d')]);
        }
        if($this->closed_at !=""){ 
            $query->andFilterWhere(['>=', 'closed_at', Yii::$app->formatter->asDate($this->closed_at, 'php:Y-m-d')]);
        }

        if($this->data_dal !=""){ 
            $query->andFilterWhere(['>=', 'created_at', Yii::$app->formatter->asDate($this->data_dal, 'php:Y-m-d')]);
        }

        if($this->data_al != ""){
            $query->andFilterWhere(['<=', 'created_at', Yii::$app->formatter->asDate($this->data_al, 'php:Y-m-d')]);
        }

        if($this->organizzazione!=""){
            $query->andFilterWhere([
                'ilike', 
                'organizzazione', 
                $this->getAttribute('organizzazione')]);
        }

        $dataProvider->sort->attributes['organizzazione'] = [
            'asc' => ['organizzazione' => SORT_ASC],
            'desc' => ['organizzazione' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['num_elenco_territoriale'] = [
            'asc' => ['num_elenco_territoriale' => SORT_ASC],
            'desc' => ['num_elenco_territoriale' => SORT_DESC],
        ];

        
        $dataProvider->sort->attributes['mese'] = [
            'asc' => [ 'mese_int' => SORT_ASC],
            'desc' => ['mese_int' => SORT_DESC],
        ];
        
        if(!empty($this->mese)){
            $query->andFilterWhere([ 'mese_int' => $this->mese ]);
        }

        $dataProvider->sort->attributes['anno'] = [
            'asc' => ['anno' => SORT_ASC],
            'desc' => ['anno' => SORT_DESC],
        ];



        if($this->anno) {
            $query->andFilterWhere(['anno' => $this->anno]);
        }

        if($this->num_protocollo!="") {
            $attr = $this->getAttribute('num_protocollo');
            if( preg_match ("/\*/", $attr) > 0  ) {
                $str = trim ( str_replace("*", "", $attr) );
                $e = UtlEvento::find()->where(['num_protocollo'=>$str])->one();
                $ids = [];
                if($e) :
                    $ids[] = $e->id;
                    $events = UtlEvento::find()->where(['idparent'=>$e->id])->all();
                    foreach ($events as $event) : $ids[] = $event->id; endforeach;
                endif;                
                $query->andFilterWhere(['in', 'id_evento', $ids ]);                
            } else {
                $query->andFilterWhere(['ilike', 'num_protocollo', $this->num_protocollo ]);
            }
        }

        if($this->targa != ""){
            $query->andFilterWhere(['iLike', 'targa', $this->targa ]);
        }

        $dataProvider->sort->attributes['num_protocollo'] = [
            'asc' => ['id_evento' => SORT_ASC],
            'desc' => ['id_evento' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['tipologia'] = [
            'asc' => ['tipologia' => SORT_ASC],
            'desc' => ['tipologia' => SORT_DESC],
        ];

        if(!empty($this->tipologia)){
            if($this->tipologia == '__') {
                $query->andWhere('tipologia is null');
            } else {
                $query->andFilterWhere(['tipologia' => $this->tipologia]);
            }
        }

        $dataProvider->sort->attributes['sottotipologia'] = [
            'asc' => ['sottotipologia' => SORT_ASC],
            'desc' => ['sottotipologia' => SORT_DESC],
        ];

        if($this->getAttribute('sottotipologia')!=""){
            $query->andFilterWhere(['sottotipologia' => $this->sottotipologia ]);
        }

        $dataProvider->sort->attributes['provincia_sigla'] = [
            'asc' => ['provincia_sigla' => SORT_ASC],
            'desc' => ['provincia_sigla' => SORT_DESC],
        ];

        if($this->provincia_sigla!=""){
            
            $query->andFilterWhere( ['id_provincia' => $this->provincia_sigla ] );
        }

        if($this->coc!=""){
            
            $query->andFilterWhere( ['coc' => $this->coc ] );
        }

        if(!empty( $this->id_gestore ) ){
            
            $query->andFilterWhere( ['id_gestore' => $this->id_gestore ] );
        }

        $dataProvider->sort->attributes['comune'] = [
            'asc' => ['comune' => SORT_ASC],
            'desc' => ['comune' => SORT_DESC],
        ];

        
        if($this->comune !=[]){
            $query->andFilterWhere( [ 'id_comune' => $this->comune  ] );
        }

        if($this->indirizzo !=""){
            $query->andFilterWhere(['ilike', 'indirizzo', $this->indirizzo ]);
        }

        $dataProvider->sort->attributes['targa'] = [
            'asc' => ['targa' => SORT_ASC],
            'desc' => ['targa' => SORT_DESC],
        ];

        if($this->modello_attrezzatura !=""){
            $query->andFilterWhere(['iLike', 'modello_attrezzatura', $this->modello_attrezzatura ]);
        }

        $dataProvider->sort->attributes['modello_attrezzatura'] = [
            'asc' => ['modello_attrezzatura' => SORT_ASC],
            'desc' => ['modello_attrezzatura' => SORT_DESC],
        ];



        if($this->num_elenco_territoriale !=""){
            $query->andFilterWhere(['num_elenco_territoriale' => $this->num_elenco_territoriale ]);
        }

        $dataProvider->sort->attributes['indirizzo_sede'] = [
            'asc' => ['indirizzo_sede' => SORT_ASC],
            'desc' => ['indirizzo_sede' => SORT_DESC],
        ];

        if($this->indirizzo_sede !=""){
            $query->andFilterWhere(['ilike', 'indirizzo_sede', $this->indirizzo_sede ]);
        }

        $dataProvider->sort->attributes['tipo_sede'] = [
            'asc' => ['tipo_sede' => SORT_ASC],
            'desc' => ['tipo_sede' => SORT_DESC],
        ];

        if($this->tipo_sede != ""){
            $query->andFilterWhere([ 'tipo_sede' => $this->tipo_sede ]);
        }

        

        $dataProvider->sort->attributes['tipo_attrezzatura'] = [
            'asc' => ['tipo_attrezzatura' => SORT_ASC],
            'desc' => ['tipo_attrezzatura' => SORT_DESC],
        ];

        if($this->tipo_attrezzatura !=""){
            $query->andFilterWhere( ['id_tipo_attrezzatura' => $this->tipo_attrezzatura ] );
        }

        $dataProvider->sort->attributes['tipo_automezzo'] = [
            'asc'  => ['tipo_automezzo' => SORT_ASC],
            'desc' => ['tipo_automezzo' => SORT_DESC],
        ];

        if($this->tipo_automezzo !=""){
            $query->andFilterWhere( ['id_tipo_automezzo' => $this->tipo_automezzo] );
        }

        $dataProvider->sort->attributes['durata'] = [
            'asc'  => ['durata' => SORT_ASC],
            'desc' => ['durata' => SORT_DESC],
        ];

        if(!empty($this->durata)){
            $query->andFilterWhere( ['durata' => $this->durata] );
        }

        
        $query->andFilterWhere(['ilike', 'note', $this->note]);


        if(isset($params['UtlIngaggioSearch']['aggregatore']) && $params['UtlIngaggioSearch']['aggregatore'] != '') {
            

            $query->andWhere(['or',
                [ 'ilike','aggregatore_automezzi'=>$params['UtlIngaggioSearch']['aggregatore'] ],
                [ 'ilike','aggregatore_attrezzature'=>$params['UtlIngaggioSearch']['aggregatore'] ],
            ]);
        }



        return $dataProvider;
    }

    public function getStatoColor()
    {
        switch($this->stato){
            case 'In attesa di conferma':
            return 'yellow';
            break;
            case 'Confermato':
            return 'green';
            break;
            case 'Rifiutato':
            return 'red';
            break;
            case 'Chiuso':
            return 'grey';
            break;
        }
    }
    
}
