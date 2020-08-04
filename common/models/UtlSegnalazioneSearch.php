<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UtlSegnalazione;

/**
 * UtlSegnalazioneSearch represents the model behind the search form about `common\models\UtlSegnalazione`.
 */
class UtlSegnalazioneSearch extends UtlSegnalazione
{
    public $ruolo_segnalatore;

    public function attributes() {
        
        return array_merge(parent::attributes(), [
            'attachment', 'attachment.filename', 'utente', 'utente.tipo', 'utente.id_ruolo_segnalatore', 'comune', 'comune.provincia', 'comune.comune', 'extras',
            'data_dal', 'data_al', 
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'distanza', 'utente.id_ruolo_segnalatore'], 'integer'],
            [['foto', 'attachment', 'note', 'direzione', 'tipologia_evento', 'stato', 'indirizzo', 'comune.comune', 'comune.provincia', 'extras'], 'safe'],
            [['lat', 'lon'], 'number'],
            [['fonte'],'string'],
            [['pericolo', 'feriti', 'interruzione_viabilita', 'aiuto_segnalatore'], 'boolean'],
            [['dataora_segnalazione', 'idutente', 'num_protocollo', 'utente', 'utente.tipo', 'ruolo_segnalatore','data_dal', 'data_al'], 'safe'],
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
    public function search($operatore=null,$params)
    {

        
        $query = UtlSegnalazione::find()->joinWith(['utente', 'utente.ruoloSegnalatore', 'comune', 'comune.provincia', 'attachment', 'extras', 'tipologia'])
                    ->groupBy(['utl_segnalazione.id','utl_utente.tipo', 'utl_ruolo_segnalatore.descrizione','con_segnalazione_extra.id','loc_comune.comune','loc_provincia.provincia','utl_tipologia.tipologia']);
        if (!empty($operatore)) {
            $query->where(['idsalaoperativa' => $operatore->idsalaoperativa]);
        }


        if(!empty($params['stato'])){
            $query->andWhere(['stato'=>$params['stato']]);
        }

        if(!empty($params['tipologia'])){
            $query->andWhere(['tipologia_evento' => $params['tipologia']]);
        }
        
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,

        ]);

        
        $dataProvider->sort->attributes['tipologia_evento'] = [
            'asc' => ['utl_tipologia.tipologia' => SORT_ASC],
            'desc' => ['utl_tipologia.tipologia' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['attachment'] = [
            'asc' => ['utl_segnalazione_attachments.filename' => SORT_ASC],
            'desc' => ['utl_segnalazione_attachments.filename' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['utente'] = [
            'asc' => ['utl_utente.tipo' => SORT_ASC],
            'desc' => ['utl_utente.tipo' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['ruolo_segnalatore'] = [
            'asc' => ['utl_ruolo_segnalatore.descrizione' => SORT_ASC],
            'desc' => ['utl_ruolo_segnalatore.descrizione' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['comune.comune'] = [
            'asc' => ['loc_comune.comune' => SORT_ASC],
            'desc' => ['loc_comune.comune' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['comune.provincia'] = [
            'asc' => ['loc_provincia.provincia' => SORT_ASC],
            'desc' => ['loc_provincia.provincia' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['extras'] = [
            'asc' => ['con_segnalazione_extra.id' => SORT_ASC],
            'desc' => ['con_segnalazione_extra.id' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['num_protocollo'] = [
            'asc' => ['utl_segnalazione.id' => SORT_ASC],
            'desc' => ['utl_segnalazione.id' => SORT_DESC],
        ];



        $this->load($params);



        if (!$this->validate()) {
            return $dataProvider;
        }

        
        $query->andFilterWhere([
            'id' => $this->id,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'distanza' => $this->distanza,
            'pericolo' => $this->pericolo,
            'feriti' => $this->feriti,
            'interruzione_viabilita' => $this->interruzione_viabilita,
            'aiuto_segnalatore' => $this->aiuto_segnalatore,
            'fonte' => $this->fonte,
        ]);
        
        $query->andFilterWhere(['like', 'foto', $this->foto])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'num_protocollo', $this->num_protocollo])
            ->andFilterWhere(['like', 'indirizzo', $this->indirizzo])
            ->andFilterWhere(['like', 'direzione', $this->direzione]);

        if(!empty($this->tipologia_evento)) {
            $tipo = \common\models\UtlTipologia::findOne($this->tipologia_evento);
            
            if($tipo) {
                if( empty( $tipo->idparent ) ) {
                    $query->andFilterWhere(['tipologia_evento'=>$this->tipologia_evento]);
                } else {                    
                    $query->andFilterWhere(['sottotipologia_evento'=>$this->tipologia_evento]);
                }
            }
        }

        if($this->getAttribute('data_dal')!=""){
            $query->andFilterWhere(['>=', 'dataora_segnalazione', Yii::$app->formatter->asDate($this->getAttribute('data_dal'), 'php:Y-m-d')]);
        }

        if($this->getAttribute('data_al')!=""){
            $query->andFilterWhere(['<=', 'dataora_segnalazione', Yii::$app->formatter->asDate($this->getAttribute('data_al'), 'php:Y-m-d')]);
        }

        if(!empty($this->stato)){
            $query->andFilterWhere(['stato' => $this->stato]);
        }

        if(!empty($this->attachment)){
            $query->andFilterWhere(['ilike', 'utl_segnalazione_attachments.filename', $this->getAttribute('attachment')]);
        }

        if(!empty($this->ruolo_segnalatore)){
            $query->andFilterWhere(['utl_utente.id_ruolo_segnalatore' => $this->ruolo_segnalatore]);
        }

        
        if(!empty($this->utente)){
            $query->andFilterWhere(['=', 'utl_utente.tipo', $this->getAttribute('utente')]);
        }
        
        if (!empty($this->dataora_segnalazione)) {

          $datetimeFrom = date('Y-m-d', strtotime($this->dataora_segnalazione)) .' 00:00:00';
          $datetimeTo = date('Y-m-d', strtotime($this->dataora_segnalazione)) .' 23:59:59';
          $query->andFilterWhere(['BETWEEN', 'dataora_segnalazione', $datetimeFrom, $datetimeTo]);

        }

        $query->andFilterWhere(['loc_comune.id' => $this->getAttribute('comune.comune')]);
        $query->andFilterWhere(['loc_provincia.id' => $this->getAttribute('comune.provincia')]);

        if(!Yii::$app->request->get('sort')) $query->orderBy(['dataora_segnalazione'=>SORT_DESC]);


        return $dataProvider;
    }

    /**
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByEvento($idEvento, $params)
    {
        $idEvento = intval($idEvento);
        
        $query = UtlSegnalazione::find()
                                ->joinWith(['utente', 'comune', 'comune.provincia', 'attachment', 'extras', 'tipologia'])
                                ->where(['or',
                                    "utl_segnalazione.id in (select idsegnalazione from con_evento_segnalazione where idevento = $idEvento)",
                                    "utl_segnalazione.id in (select idsegnalazione from con_evento_segnalazione where idevento in (SELECT id FROM utl_evento WHERE idparent = $idEvento))"
                                ])
                                ->groupBy(['utl_segnalazione.id','utl_utente.tipo','loc_comune.comune','loc_provincia.provincia','utl_tipologia.tipologia']);
                                
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['dataora_segnalazione'=>SORT_DESC]]
        ]);

        
        $dataProvider->sort->attributes['attachment'] = [
            'asc' => ['utl_segnalazione_attachments.filename' => SORT_ASC],
            'desc' => ['utl_segnalazione_attachments.filename' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['num_protocollo'] = [
            'asc' => ['utl_segnalazione.id' => SORT_ASC],
            'desc' => ['utl_segnalazione.id' => SORT_DESC],
        ];

        
        $dataProvider->sort->attributes['utente'] = [
            'asc' => ['utl_utente.tipo' => SORT_ASC],
            'desc' => ['utl_utente.tipo' => SORT_DESC],
        ];

        
        $dataProvider->sort->attributes['comune.comune'] = [
            'asc' => ['loc_comune.comune' => SORT_ASC],
            'desc' => ['loc_comune.comune' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['comune.provincia'] = [
            'asc' => ['loc_provincia.provincia' => SORT_ASC],
            'desc' => ['loc_provincia.provincia' => SORT_DESC],
        ];

        
        $dataProvider->sort->attributes['tipologia_evento'] = [
            'asc' => ['utl_tipologia.tipologia' => SORT_ASC],
            'desc' => ['utl_tipologia.tipologia' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'lat' => $this->lat,
            'lon' => $this->lon,
            'distanza' => $this->distanza,
            'pericolo' => $this->pericolo,
            'feriti' => $this->feriti,
            'interruzione_viabilita' => $this->interruzione_viabilita,
            'aiuto_segnalatore' => $this->aiuto_segnalatore,
        ]);

        $query->andFilterWhere(['ilike', 'foto', $this->foto])
            ->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'num_protocollo', $this->num_protocollo])
            ->andFilterWhere(['ilike', 'indirizzo', $this->indirizzo])
            ->andFilterWhere(['ilike', 'direzione', $this->direzione]);

        if(!empty($this->tipologia_evento)) {
            $tipo = \common\models\UtlTipologia::findOne($this->tipologia_evento);
            
            if($tipo) {
                if( empty( $tipo->idparent ) ) {
                    $query->andFilterWhere(['tipologia_evento'=>$this->tipologia_evento]);
                } else {                    
                    $query->andFilterWhere(['sottotipologia_evento'=>$this->tipologia_evento]);
                }
            }
        }

        if(!empty($this->stato)){
            $query->andFilterWhere(['=', 'stato', (int)$this->stato]);
        }

        if(!empty($this->attachment)){
            $query->andFilterWhere(['ilike', 'utl_segnalazione_attachments.filename', $this->getAttribute('attachment')]);
        }

        if(!empty($this->utente)){
            $query->andFilterWhere(['=', 'utl_utente.tipo', $this->getAttribute('utente')]);
        }

        if (!empty($this->dataora_segnalazione)) {

            $datetimeFrom = date('Y-m-d', strtotime($this->dataora_segnalazione)) .' 00:00:00';
            $datetimeTo = date('Y-m-d', strtotime($this->dataora_segnalazione)) .' 23:59:59';
            $query->andFilterWhere(['BETWEEN', 'dataora_segnalazione', $datetimeFrom, $datetimeTo]);
            
        }

        $query->andFilterWhere(['loc_comune.id' => $this->getAttribute('comune.comune')]);
        $query->andFilterWhere(['loc_provincia.id' => $this->getAttribute('comune.provincia')]);

        $t = $query->createCommand()->getRawSql();
        return $dataProvider;
    }
}
