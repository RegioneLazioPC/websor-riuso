<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UtlEvento;
use yii\db\Query;

/**
 * UtlEventoSearch represents the model behind the search form about `common\models\UtlEvento`.
 */
class UtlEventoSearch extends UtlEvento
{

    public function attributes() {
        return array_merge(parent::attributes(), [
            'comune', 'comune.provincia', 'comune.comune', 'operatori'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tipologia_evento', 'distanza','idparent', 'id_gestore_evento','has_coc','id_sottostato_evento'], 'integer'],
            [['note', 'direzione', 'dataora_evento', 'dataora_modifica', 'closed_at', 'stato', 'indirizzo', 'num_protocollo', 'comune.comune', 'comune.provincia'], 'safe'],
            [['lat', 'lon'], 'number'],
            [['pericolo', 'feriti', 'interruzione_viabilita', 'aiuto_segnalatore'], 'boolean'],
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
     * 
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        
        $query = UtlEvento::find()->where("stato != 'Chiuso'")
        ->joinWith(['richiesteMezziAerei','comune','comune.provincia','tipologia'])
        ->groupBy(['utl_evento.id','loc_comune.comune','loc_provincia.provincia','utl_tipologia.tipologia']);

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['dataora_evento'=>SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['comune.comune'] = [
            'asc' => ['loc_comune.comune' => SORT_ASC],
            'desc' => ['loc_comune.comune' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['comune.provincia'] = [
            'asc' => ['loc_provincia.provincia' => SORT_ASC],
            'desc' => ['loc_provincia.provincia' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['operatori'] = [
            'asc' => ['loc_provincia.provincia' => SORT_ASC],
            'desc' => ['loc_provincia.provincia' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['num_protocollo'] = [
            'asc' => ['utl_evento.id' => SORT_ASC],
            'desc' => ['utl_evento.id' => SORT_DESC],
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
            'has_coc' => $this->has_coc,
            'id_sottostato_evento' => $this->id_sottostato_evento,
            'feriti' => $this->feriti,
            'interruzione_viabilita' => $this->interruzione_viabilita,
            'aiuto_segnalatore' => $this->aiuto_segnalatore,            
            'id_gestore_evento' => $this->id_gestore_evento,            
        ]);

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

        if($this->idparent || $this->idparent == 0) :
            $query->andWhere( 'utl_evento.idparent IS NULL' );
        endif;

        $query->andFilterWhere(['like', 'note', $this->note])
                ->andFilterWhere(['like', 'indirizzo', $this->indirizzo])
                ->andFilterWhere(['like', 'num_protocollo', $this->num_protocollo])
                ->andFilterWhere(['like', 'direzione', $this->direzione]);

        if(!empty($this->stato)){
            $query->andFilterWhere(['=', 'stato', $this->stato]);
        }

        if (!empty($this->dataora_evento)) {
          $datetimeFrom = date('Y-m-d', strtotime($this->dataora_evento)) .' 00:00:00';
          $datetimeTo = date('Y-m-d', strtotime($this->dataora_evento)) .' 23:59:59';
          $query->andFilterWhere(['BETWEEN', 'dataora_evento', $datetimeFrom, $datetimeTo]);
        }

        if (!empty($this->closed_at)) {
          $datetimeFrom = date('Y-m-d', strtotime($this->closed_at)) .' 00:00:00';
          $datetimeTo = date('Y-m-d', strtotime($this->closed_at)) .' 23:59:59';
          $query->andFilterWhere(['BETWEEN', 'closed_at', $datetimeFrom, $datetimeTo]);
        }


        $query->andFilterWhere(['loc_comune.id' => $this->getAttribute('comune.comune')]);
        $query->andFilterWhere(['loc_provincia.id' => $this->getAttribute('comune.provincia')]);

        
        if(!Yii::$app->request->get('sort')) $query->orderBy(['id'=>SORT_DESC]);


        return $dataProvider;
    }

    /**
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchByOperatore($params)
    {

        $eventi = ConOperatoreEvento::find()->select('idevento')
        ->where(['idoperatore' => Yii::$app->user->identity->operatore->id])->all();
        $idsEventi = array();
        foreach ($eventi as $evento){
            $idsEventi[] = $evento->idevento;
        }

        $query = UtlEvento::find()->where(['id' => $idsEventi]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['dataora_evento'=>SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['comune.comune'] = [
            'asc' => ['loc_comune.comune' => SORT_ASC],
            'desc' => ['loc_comune.comune' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['comune.provincia'] = [
            'asc' => ['loc_provincia.provincia' => SORT_ASC],
            'desc' => ['loc_provincia.provincia' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['num_protocollo'] = [
            'asc' => ['utl_evento.id' => SORT_ASC],
            'desc' => ['utl_evento.id' => SORT_DESC],
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
            'id_gestore_evento' => $this->id_gestore_evento, 
            'idparent' => $this->idparent
        ]);

        $query->andFilterWhere(['ilike', 'note', $this->note])
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
            $query->andFilterWhere(['=', 'stato', $this->stato]);
        }

        if($this->idparent || $this->idparent == 0) :
            $query->andWhere( 'idparent IS NULL' );
        endif;

        $query->andFilterWhere(['loc_comune.id' => $this->getAttribute('comune.comune')]);
        $query->andFilterWhere(['ilike', 'loc_provincia.provincia', $this->getAttribute('comune.provincia.sigla')]);

        if (!empty($this->dataora_evento)) {
            $datetimeFrom = date('Y-m-d', strtotime($this->dataora_evento)) .' 00:00:00';
            $datetimeTo = date('Y-m-d', strtotime($this->dataora_evento)) .' 23:59:59';
            $query->andFilterWhere(['BETWEEN', 'dataora_evento', $datetimeFrom, $datetimeTo]);
        }

        return $dataProvider;
    }

    /**
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchClosed($params)
    {
        $query = UtlEvento::find()->where(['stato' => 'Chiuso'])->andWhere(['archived'=>0])->joinWith(['comune','comune.provincia'])->groupBy('utl_evento.id');

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['dataora_evento'=>SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['comune.comune'] = [
            'asc' => ['loc_comune.comune' => SORT_ASC],
            'desc' => ['loc_comune.comune' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['comune.provincia'] = [
            'asc' => ['loc_provincia.provincia' => SORT_ASC],
            'desc' => ['loc_provincia.provincia' => SORT_DESC],
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
            'id_gestore_evento' => $this->id_gestore_evento, 
            'aiuto_segnalatore' => $this->aiuto_segnalatore,
        ]);

        if($this->idparent || $this->idparent == 0) :
            $query->andWhere( 'utl_evento.idparent IS NULL' );
        endif;

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

        $query->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'num_protocollo', $this->num_protocollo])
            ->andFilterWhere(['ilike', 'direzione', $this->direzione]);

        if(!empty($this->stato)){
            $query->andFilterWhere(['=', 'stato', (int)$this->stato]);
        }

        $query->andFilterWhere(['loc_comune.id' => $this->getAttribute('comune.comune')]);
        $query->andFilterWhere([ 'loc_provincia.id' => $this->getAttribute('comune.provincia')]);

        if (!empty($this->dataora_evento) && !empty($this->closed_at)) {

            $datetimeFrom = date('Y-m-d', strtotime($this->dataora_evento)) .' 00:00:00';
            $datetimeTo = date('Y-m-d', strtotime($this->closed_at)) .' 23:59:59';
            $query->andFilterWhere(['BETWEEN', 'dataora_evento', $datetimeFrom, $datetimeTo]);

        }

        if (!empty($this->closed_at) && empty($this->dataora_evento)) {
            $datetimeTo = date('Y-m-d', strtotime($this->closed_at)) .' 23:59:59';
            $query->andFilterWhere(['<=', 'closed_at', $datetimeTo]);
        }

        if (empty($this->closed_at) && !empty($this->dataora_evento)) {
            $datetimeFrom = date('Y-m-d', strtotime($this->dataora_evento)) .' 00:00:00';
            $query->andFilterWhere(['>=', 'dataora_evento', $datetimeFrom]);
        }

        return $dataProvider;
    }


    /**
     * Eventi archiviati
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchArchived($params)
    {
        $query = UtlEvento::find()->where(['stato' => 'Chiuso'])->andWhere(['archived'=>1])->joinWith(['comune','comune.provincia'])->groupBy('utl_evento.id');

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['dataora_evento'=>SORT_DESC]]
        ]);

        
        $dataProvider->sort->attributes['comune.comune'] = [
            'asc' => ['loc_comune.comune' => SORT_ASC],
            'desc' => ['loc_comune.comune' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['comune.provincia'] = [
            'asc' => ['loc_provincia.provincia' => SORT_ASC],
            'desc' => ['loc_provincia.provincia' => SORT_DESC],
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
            'id_gestore_evento' => $this->id_gestore_evento, 
            'aiuto_segnalatore' => $this->aiuto_segnalatore,
        ]);

        if($this->idparent || $this->idparent == 0) :
            $query->andWhere( 'utl_evento.idparent IS NULL' );
        endif;

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

        $query->andFilterWhere(['ilike', 'note', $this->note])
            ->andFilterWhere(['ilike', 'num_protocollo', $this->num_protocollo])
            ->andFilterWhere(['ilike', 'direzione', $this->direzione]);

        if(!empty($this->stato)){
            $query->andFilterWhere(['=', 'stato', (int)$this->stato]);
        }

        $query->andFilterWhere(['loc_comune.id' => $this->getAttribute('comune.comune')]);
        $query->andFilterWhere([ 'loc_provincia.id' => $this->getAttribute('comune.provincia')]);

        if (!empty($this->dataora_evento) && !empty($this->closed_at)) {

            $datetimeFrom = date('Y-m-d', strtotime($this->dataora_evento)) .' 00:00:00';
            $datetimeTo = date('Y-m-d', strtotime($this->closed_at)) .' 23:59:59';
            $query->andFilterWhere(['BETWEEN', 'dataora_evento', $datetimeFrom, $datetimeTo]);

        }

        if (!empty($this->closed_at) && empty($this->dataora_evento)) {
            $datetimeTo = date('Y-m-d', strtotime($this->closed_at)) .' 23:59:59';
            $query->andFilterWhere(['<=', 'closed_at', $datetimeTo]);
        }

        if (empty($this->closed_at) && !empty($this->dataora_evento)) {
            $datetimeFrom = date('Y-m-d', strtotime($this->dataora_evento)) .' 00:00:00';
            $query->andFilterWhere(['>=', 'dataora_evento', $datetimeFrom]);
        }

        return $dataProvider;
    }
}
