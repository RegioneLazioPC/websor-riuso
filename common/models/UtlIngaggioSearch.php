<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UtlIngaggio;
use common\models\UtlEvento;

use common\models\UtlAggregatoreTipologie;
/**
 * UtlIngaggioSearch represents the model behind the search form of `common\models\UtlIngaggio`.
 */
class UtlIngaggioSearch extends UtlIngaggio
{
    public $anno, $mese, $aggregatore, $tipologia_evento;

    public function attributes() {
        
        return array_merge(parent::attributes(), [
            'data_dal', 'data_al', 'organizzazione', 'organizzazione.denominazione', 'evento', 'evento.num_protocollo', 'evento.id_gestore_evento',
            'automezzo', 'automezzo.targa', 'automezzo.tipo.descrizione', 
            'attrezzatura', 'attrezzatura.modello', 'attrezzatura.tipo.descrizione','evento.has_coc',
            'sede.indirizzo',
            'evento.tipologia.tipologia', 'evento.sottotipologia.tipologia', 'evento.comune.comune', 'evento.comune.provincia.sigla',
            'organizzazione.ref_id', 'sede.tipo', 'sede.locComune.provincia.sigla',
            'indirizzo_luogo','feedbackRl.created_at'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'idevento', 'idorganizzazione', 'idsede', 'idautomezzo', 'idattrezzatura', 'stato', 'organizzazione.ref_id', 'evento.id_gestore_evento'], 'integer'],
            [['organizzazione.denominazione', 'evento.num_protocollo','automezzo.targa','attrezzatura.modello',
               'automezzo.tipo.descrizione', 'attrezzatura.tipo.descrizione',
               'sede.indirizzo', 'evento.tipologia.tipologia', 'evento.sottotipologia.tipologia','evento.has_coc',
               'indirizzo_luogo',
               'sede.tipo', 'sede.locComune.provincia.sigla',
               'aggregatore', 'anno', 'mese', 'tipologia_evento' ], 'string'],
            [['evento.comune.provincia.sigla', 'evento.comune.comune',
            'note', 'updated_at', 'closed_at','data_dal', 'data_al'], 'safe'],
            [['created_at','feedbackRl.created_at'], 'date'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    
    /**
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $to_check = false)
    {
        
        $query = UtlIngaggio::find()->joinWith(
            [
                'sede', 'organizzazione', 'evento', 'automezzo', 'attrezzatura', 
                'evento.tipologia as tipologia', 'evento.sottotipologia as sottotipologia', 
                'automezzo.tipo', 'automezzo.tipo.aggregatoriUnique as aggregatore_automezzo',
                'attrezzatura.tipo', 'attrezzatura.tipo.aggregatoriUnique as aggregatore_attrezzatura',
                'evento.comune', 'evento.comune.provincia','feedbackRl'
            ]
        );

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        $this->load($params);

        

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'idevento' => $this->idevento,
            'idorganizzazione' => $this->idorganizzazione,
            'idsede' => $this->idsede,
            'idautomezzo' => $this->idautomezzo,
            'idattrezzatura' => $this->idattrezzatura,
            'utl_ingaggio.stato' => $this->stato,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'closed_at' => $this->closed_at,
        ]);

        if($this->getAttribute('data_dal')!=""){
            $query->andFilterWhere(['>=', 'created_at', Yii::$app->formatter->asDate($this->getAttribute('data_dal'), 'php:Y-m-d')]);
        }

        if($this->getAttribute('data_al')!=""){
            $query->andFilterWhere(['<=', 'created_at', Yii::$app->formatter->asDate($this->getAttribute('data_al'), 'php:Y-m-d')]);
        }

        if($this->getAttribute('organizzazione.denominazione')!=""){
            $query->andFilterWhere([
                'ilike', 
                'vol_organizzazione.denominazione', 
                $this->getAttribute('organizzazione.denominazione')]);
        }

        $dataProvider->sort->attributes['organizzazione.denominazione'] = [
            'asc' => ['vol_organizzazione.denominazione' => SORT_ASC],
            'desc' => ['vol_organizzazione.denominazione' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['organizzazione.ref_id'] = [
            'asc' => ['vol_organizzazione.ref_id' => SORT_ASC],
            'desc' => ['vol_organizzazione.ref_id' => SORT_DESC],
        ];

        
        $dataProvider->sort->attributes['mese'] = [
            'asc' => ['utl_ingaggio.created_at' => SORT_ASC],
            'desc' => ['utl_ingaggio.created_at' => SORT_DESC],
        ];
        
        if(isset($params['UtlIngaggioSearch']) && isset($params['UtlIngaggioSearch']['mese'])!=""){
            $query->andFilterWhere(['EXTRACT(MONTH FROM utl_ingaggio.created_at)' => $params['UtlIngaggioSearch']['mese']]);
        }

        $dataProvider->sort->attributes['anno'] = [
            'asc' => ['utl_ingaggio.created_at' => SORT_ASC],
            'desc' => ['utl_ingaggio.created_at' => SORT_DESC],
        ];



        if(isset($params['UtlIngaggioSearch']) && isset($params['UtlIngaggioSearch']['anno'])!="") {
            $query->andFilterWhere(['EXTRACT(YEAR FROM utl_ingaggio.created_at)' => $params['UtlIngaggioSearch']['anno']]);
        }

        if($this->getAttribute('evento.num_protocollo')!="") {
            $attr = $this->getAttribute('evento.num_protocollo');
            if( preg_match ("/\*/", $attr) > 0  ) {
                $str = trim ( str_replace("*", "", $attr) );
                $e = UtlEvento::find()->where(['num_protocollo'=>$str])->one();
                $ids = [];
                if($e) :
                    $ids[] = $e->id;
                    $events = UtlEvento::find()->where(['idparent'=>$e->id])->all();
                    foreach ($events as $event) : $ids[] = $event->id; endforeach;
                endif;                
                $query->andFilterWhere(['in', 'utl_evento.id', $ids ]);                
            } else {
                $query->andFilterWhere(['iLike', 'utl_evento.num_protocollo', $this->getAttribute('evento.num_protocollo')]);
            }
        }

        if($this->getAttribute('automezzo.targa')!=""){
            $query->andFilterWhere(['iLike', 'utl_automezzo.targa', $this->getAttribute('automezzo.targa')]);
        }

        $dataProvider->sort->attributes['evento.num_protocollo'] = [
            'asc' => ['utl_evento.id' => SORT_ASC],
            'desc' => ['utl_evento.id' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['tipologia_evento'] = [
            'asc' => ['tipologia.tipologia' => SORT_ASC],
            'desc' => ['tipologia.tipologia' => SORT_DESC],
        ];

        if(!empty($this->tipologia_evento)){
            if($this->tipologia_evento == '__') {
                $query->andWhere('tipologia.tipologia is null');
            } else {
                $query->andFilterWhere(['tipologia.tipologia' => $this->tipologia_evento]);
            }
        }

        $dataProvider->sort->attributes['evento.sottotipologia.tipologia'] = [
            'asc' => ['sottotipologia.tipologia' => SORT_ASC],
            'desc' => ['sottotipologia.tipologia' => SORT_DESC],
        ];

        if($this->getAttribute('evento.sottotipologia.tipologia')!=""){
            $query->andFilterWhere(['sottotipologia.tipologia' => $this->getAttribute('evento.sottotipologia.tipologia')]);
        }

        $dataProvider->sort->attributes['evento.comune.provincia.sigla'] = [
            'asc' => ['loc_provincia.sigla' => SORT_ASC],
            'desc' => ['loc_provincia.sigla' => SORT_DESC],
        ];

        if($this->getAttribute('evento.comune.provincia.sigla')!=""){
            
            $query->andFilterWhere( ['loc_provincia.id' => $this->getAttribute('evento.comune.provincia.sigla')] );
        }

        if($this->getAttribute('evento.has_coc')!=""){
            
            $query->andFilterWhere( ['utl_evento.has_coc' => $this->getAttribute('evento.has_coc')] );
        }

        if(!empty( $this->getAttribute('evento.id_gestore_evento') ) ){
            
            $query->andFilterWhere( ['utl_evento.id_gestore_evento' => $this->getAttribute('evento.id_gestore_evento')] );
        }

        $dataProvider->sort->attributes['evento.comune.comune'] = [
            'asc' => ['loc_comune.comune' => SORT_ASC],
            'desc' => ['loc_comune.comune' => SORT_DESC],
        ];

        
        if($this->getAttribute('evento.comune.comune')!=[]){
            $query->andFilterWhere( ['loc_comune.id' => $this->getAttribute('evento.comune.comune')] );
        }

        if($this->getAttribute('indirizzo_luogo')!=""){
            $query->andFilterWhere(['or',
                ['ilike', 'utl_evento.indirizzo', $this->getAttribute('indirizzo_luogo')], 
                ['ilike', 'utl_evento.luogo', $this->getAttribute('indirizzo_luogo')], 
            ] );
        }

        $dataProvider->sort->attributes['automezzo.targa'] = [
            'asc' => ['utl_automezzo.targa' => SORT_ASC],
            'desc' => ['utl_automezzo.targa' => SORT_DESC],
        ];

        if($this->getAttribute('attrezzatura.modello')!=""){
            $query->andFilterWhere(['iLike', 'utl_attrezzatura.modello', $this->getAttribute('attrezzatura.modello')]);
        }

        $dataProvider->sort->attributes['attrezzatura.modello'] = [
            'asc' => ['utl_attrezzatura.modello' => SORT_ASC],
            'desc' => ['utl_attrezzatura.modello' => SORT_DESC],
        ];

        if($this->getAttribute('organizzazione.ref_id')!=""){
            $query->andFilterWhere(['vol_organizzazione.ref_id' => $this->getAttribute('organizzazione.ref_id')]);
        }

        $dataProvider->sort->attributes['sede.indirizzo'] = [
            'asc' => ['vol_sede.indirizzo' => SORT_ASC],
            'desc' => ['vol_sede.indirizzo' => SORT_DESC],
        ];

        if($this->getAttribute('sede.indirizzo')!=""){
            $query->andFilterWhere(['ilike', 'vol_sede.indirizzo', $this->getAttribute('sede.indirizzo')]);
        }

        $dataProvider->sort->attributes['sede.tipo'] = [
            'asc' => ['vol_sede.tipo' => SORT_ASC],
            'desc' => ['vol_sede.tipo' => SORT_DESC],
        ];

        if($this->getAttribute('sede.tipo') != ""){
            $query->andFilterWhere([ 'vol_sede.tipo' => $this->getAttribute('sede.tipo')]);
        }

        $dataProvider->sort->attributes['attrezzatura.tipo.descrizione'] = [
            'asc' => ['utl_attrezzatura_tipo.descrizione' => SORT_ASC],
            'desc' => ['utl_attrezzatura_tipo.descrizione' => SORT_DESC],
        ];

        if($this->getAttribute('attrezzatura.tipo.descrizione')!=""){
            $query->andFilterWhere( ['utl_attrezzatura_tipo.id' => $this->getAttribute('attrezzatura.tipo.descrizione')] );
        }

        $dataProvider->sort->attributes['automezzo.tipo.descrizione'] = [
            'asc'  => ['utl_automezzo_tipo.descrizione' => SORT_ASC],
            'desc' => ['utl_automezzo_tipo.descrizione' => SORT_DESC],
        ];

        if($this->getAttribute('automezzo.tipo.descrizione')!=""){
            
            $query->andFilterWhere( ['utl_automezzo_tipo.id' => $this->getAttribute('automezzo.tipo.descrizione')] );
        }

        $dataProvider->sort->attributes['aggregatore'] = [
            'asc' => [
                'aggregatore_automezzo.descrizione' => SORT_ASC, 
                'aggregatore_attrezzatura.descrizione' => SORT_ASC
            ],
            'desc' => [
                'aggregatore_automezzo.descrizione' => SORT_DESC, 
                'aggregatore_attrezzatura.descrizione' => SORT_DESC
            ]
        ];

        if(isset($params['UtlIngaggioSearch']['aggregatore']) && $params['UtlIngaggioSearch']['aggregatore'] != '') {
            $aggregatori = UtlAggregatoreTipologie::find()
                            ->where([ 'id'=>$params['UtlIngaggioSearch']['aggregatore'] ])
                            ->all();
            
            $tipi_automezzo_id = [];
            $tipi_attrezzature_id = [];

            foreach ($aggregatori as $aggregatore) {
                $tipi_automezzo = $aggregatore->getTipiAutomezzo()->all();
                $tipi_attrezzature = $aggregatore->getTipiAttrezzatura()->all();
                
                foreach ($tipi_automezzo as $automezzo) {
                    $tipi_automezzo_id[] = $automezzo->id;
                }

                foreach ($tipi_attrezzature as $attrezzatura) {
                    $tipi_attrezzature_id[] = $attrezzatura->id;
                }

            }


            $query->andWhere(['or',
                ['utl_attrezzatura_tipo.id'=>$tipi_attrezzature_id],
                ['utl_automezzo_tipo.id'=>$tipi_automezzo_id]
            ]);
        }

        if(Yii::$app->request->get('sort') && Yii::$app->request->get('sort') == 'durata' || Yii::$app->request->get('sort') == '-durata') {
            
            $query->addSelect([ 
                'utl_ingaggio.*', 
                'DATE_PART(\'minute\', utl_ingaggio.created_at - utl_ingaggio.closed_at) AS durata'
            ]);
           
        }
        
        $dataProvider->sort->attributes['durata'] = [
            'asc' => ['durata'=>SORT_ASC],
            'desc' => ['durata'=>SORT_DESC],
        ];

        

        $query->andFilterWhere(['ilike', 'utl_ingaggio.note', $this->note]);

        if($to_check) {
            $query->andWhere(['rl_feedback_to_check'=>1]);
        }

        return $dataProvider;
    }

    /**
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByEvento($id, $params)
    {
       
        $query = UtlIngaggio::find()
                ->where(['or',
                                    ['idevento' => $id],
                                    ['utl_evento.idparent' => $id]
                                ])
                ->joinWith(
            [
                'sede', 'organizzazione', 'evento', 'automezzo', 'attrezzatura', 
                'evento.tipologia as tipologia', 'evento.sottotipologia as sottotipologia', 
                'automezzo.tipo', 'automezzo.tipo.aggregatoriUnique as aggregatore_automezzo',
                'attrezzatura.tipo', 'attrezzatura.tipo.aggregatoriUnique as aggregatore_attrezzatura',
                'evento.comune', 'evento.comune.provincia'
            ]
        );
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'idorganizzazione' => $this->idorganizzazione,
            'idsede' => $this->idsede,
            'idautomezzo' => $this->idautomezzo,
            'idattrezzatura' => $this->idattrezzatura,
            'utl_ingaggio.stato' => $this->stato,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'closed_at' => $this->closed_at,
        ]);

        if($this->getAttribute('data_dal')!=""){
            $query->andFilterWhere(['>=', 'created_at', Yii::$app->formatter->asDate($this->getAttribute('data_dal'), 'php:Y-m-d H:i:s')]);
        }

        if($this->getAttribute('data_al')!=""){
            $query->andFilterWhere(['<=', 'created_at', Yii::$app->formatter->asDate($this->getAttribute('data_al'), 'php:Y-m-d H:i:s')]);
        }

        if($this->getAttribute('organizzazione.denominazione')!=""){
            $query->andFilterWhere([
                'ilike', 
                'vol_organizzazione.denominazione', 
                $this->getAttribute('organizzazione.denominazione')]);
        }

        $dataProvider->sort->attributes['attrezzatura.tipo.descrizione'] = [
            'asc' => ['utl_attrezzatura_tipo.descrizione' => SORT_ASC],
            'desc' => ['utl_attrezzatura_tipo.descrizione' => SORT_DESC],
        ];

        if($this->getAttribute('attrezzatura.tipo.descrizione')!=""){
            $query->andFilterWhere( ['utl_attrezzatura_tipo.id' => $this->getAttribute('attrezzatura.tipo.descrizione')] );
        }

        $dataProvider->sort->attributes['automezzo.tipo.descrizione'] = [
            'asc'  => ['utl_automezzo_tipo.descrizione' => SORT_ASC],
            'desc' => ['utl_automezzo_tipo.descrizione' => SORT_DESC],
        ];

        if($this->getAttribute('automezzo.tipo.descrizione')!=""){
            $query->andFilterWhere( ['utl_automezzo_tipo.id' => $this->getAttribute('automezzo.tipo.descrizione')] );
        }

        $dataProvider->sort->attributes['organizzazione.denominazione'] = [
            'asc' => ['vol_organizzazione.denominazione' => SORT_ASC],
            'desc' => ['vol_organizzazione.denominazione' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['organizzazione.ref_id'] = [
            'asc' => ['vol_organizzazione.ref_id' => SORT_ASC],
            'desc' => ['vol_organizzazione.ref_id' => SORT_DESC],
        ];

        if($this->getAttribute('organizzazione.ref_id')!=""){
            $query->andFilterWhere(['vol_organizzazione.ref_id' => $this->getAttribute('organizzazione.ref_id')]);
        }


        $query->andFilterWhere(['ilike', 'utl_ingaggio.note', $this->note]);

        return $dataProvider;
    }
}
