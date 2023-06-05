<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\UtlUtente;

/**
 * UtlUtenteSearch represents the model behind the search form about `common\models\UtlUtente`.
 */
class UtlUtenteSearch extends UtlUtente
{

    public $created_at, $status, $nome_organizzazione, $data_registrazione_dal, $data_registrazione_al, $specializzazione, $id_organizzazione;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'iduser','id_ruolo_segnalatore','tipo','enabled', 'id_organizzazione', 'specializzazione'], 'integer'],
            [['nome', 'cognome', 'nome_organizzazione', 'codfiscale', 'data_nascita', 'luogo_nascita', 'telefono', 'email', 'username', 'created_at', 'status'], 'safe'],
            [['data_registrazione_dal', 'data_registrazione_al', 'smscode'], 'safe'],
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
    public function search($params)
    {
        
        $query = UtlUtente::find()->joinWith(['user','anagrafica','anagrafica.comuneResidenza as comune_residenza', 'organizzazione'])
        ->where([ 'or', 
            'codice_attivazione is not null', 
            'iduser is not null'
        ]);
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created_at' => SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['username'] = [
            'asc' => ['user.username' => SORT_ASC],
            'desc' => ['user.username' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['created_at'] = [
            'asc' => ['user.created_at' => SORT_ASC],
            'desc' => ['user.created_at' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['status'] = [
            'asc' => ['user.status' => SORT_ASC],
            'desc' => ['user.status' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['nome_organizzazione'] = [
            'asc' => ['vol_organizzazione.denominazione' => SORT_ASC],
            'desc' => ['vol_organizzazione.denominazione' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['id_organizzazione'] = [
            'asc' => ['vol_organizzazione.ref_id' => SORT_ASC],
            'desc' => ['vol_organizzazione.ref_id' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        

        
        $query->andFilterWhere([
            'id' => $this->id,
            'iduser' => $this->iduser,
            'utl_anagrafica.data_nascita' => $this->data_nascita,
            'id_ruolo_segnalatore' => $this->id_ruolo_segnalatore,
            'tipo' => $this->tipo,
            'enabled'=>$this->enabled
        ]);

        $query->andFilterWhere(['ilike', 'utl_anagrafica.nome', $this->nome])
            ->andFilterWhere(['ilike', 'utl_anagrafica.cognome', $this->cognome])
            ->andFilterWhere(['ilike', 'user.username', $this->username])
            ->andFilterWhere(['ilike', 'utl_anagrafica.codfiscale', $this->codfiscale])
            ->andFilterWhere(['ilike', 'utl_anagrafica.luogo_nascita', $this->luogo_nascita])
            ->andFilterWhere(['ilike', 'telefono', $this->telefono])
            ->andFilterWhere(['ilike', 'codice_attivazione', $this->codice_attivazione])
            ->andFilterWhere(['=', 'user.status', $this->status])
            ->andFilterWhere(['ilike', 'user.email', $this->email]);

        if(!empty($this->id_organizzazione)){
            $query->andFilterWhere(['vol_organizzazione.ref_id'=>$this->id_organizzazione]);
        }
        if(!empty($this->nome_organizzazione)){
            $query->andFilterWhere(['ilike','vol_organizzazione.denominazione',$this->nome_organizzazione]);
        }

        if(!empty($this->specializzazione)) {
            $query->joinWith('organizzazione.sezioneSpecialistica')
            ->andWhere(['tbl_sezione_specialistica.id'=>$this->specializzazione]);
        }

        if($this->data_registrazione_dal != ""){
            $query->andFilterWhere(['>=', 'FROM_UNIXTIME(user.created_at)', Yii::$app->formatter->asDate($this->data_registrazione_dal, 'php:Y-m-d')]);
        }

        if($this->data_registrazione_al != ""){
            $query->andFilterWhere(['<=', 'FROM_UNIXTIME(user.created_at)', Yii::$app->formatter->asDate($this->data_registrazione_al, 'php:Y-m-d')]);
        }

        if(Yii::$app->request->post('action') && Yii::$app->user->can('updateAppUser')) {
            $q = UtlUtente::find();
            $q = clone $query;
            $results = $q->select('utl_utente.id')->all();
            $ids = [];
            foreach ($results as $record) {
                $ids[] = $record->id;
            }


            $n = Yii::$app->request->post('action') == 'abilitate' ? 1 : 0;
            Yii::$app->db->createCommand()
                ->update(UtlUtente::tableName(), 
                [ 'enabled'=>$n ],
                [ 'id' => $ids ]) 
                ->execute();

            
        }

        
        return $dataProvider;
    }
}
