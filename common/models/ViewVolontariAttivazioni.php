<?php

namespace common\models;

use Yii;

use common\models\UtlIngaggio;
use yii\data\ActiveDataProvider;

class ViewVolontariAttivazioni extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_volontari_attivazioni';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'id',
                'denominazione',
                'localita',
                'tipologia',
                'sottotipologia',
                'mezzo',
                'targa',
                'attrezzatura',
                'modello',
                'nome',
                'cognome',
                'codfiscale',
                'full_mezzo'
            ], 'string'],
            [[
                'num_elenco_territoriale',
                'id_evento',
                'id_fronte',
                'id_tipologia',
                'id_sottotipologia',
                'id_mezzo',
                'id_attrezzatura',
                'id_attivazione',
                'id_volontario',
                'id_anagrafica'
            ], 'integer'],
            [[
                'comune',
                'provincia',
                'stato'
            ], 'safe']
        ];
    }

    public function statoLabel()
    {
        return UtlIngaggio::replaceStato( $this->stato );
    }

    public static function staticStatoLabel( $stato )
    {
        return UtlIngaggio::replaceStato( $stato );
    }
    

    public function search($params, $paginate = true)
    {
        $query = ViewVolontariAttivazioni::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) return $dataProvider;
        

        $query
            ->andFilterWhere(['ilike', 'localita', $this->localita])
            ->andFilterWhere(['ilike', 'full_mezzo', $this->full_mezzo])
            ->andFilterWhere(['ilike', 'targa', $this->targa])
            ->andFilterWhere(['ilike', 'modello', $this->modello])
            ->andFilterWhere(['ilike', 'nome', $this->nome])
            ->andFilterWhere(['ilike', 'cognome', $this->cognome])
            ->andFilterWhere(['ilike', 'codfiscale', $this->codfiscale])
            ->andFilterWhere(['comune' => $this->comune])
            ->andFilterWhere(['provincia' => $this->provincia])
            ->andFilterWhere(['ilike', 'denominazione', $this->denominazione])
            ->andFilterWhere(['tipologia' => $this->tipologia])
            ->andFilterWhere(['sottotipologia' => $this->sottotipologia])
            ->andFilterWhere(['protocollo_evento' => $this->protocollo_evento])
            ->andFilterWhere(['protocollo_fronte' => $this->protocollo_fronte])
            ->andFilterWhere(['id_evento' => $this->id_evento])
            ->andFilterWhere(['id_fronte' => $this->id_fronte])
            ->andFilterWhere(['id_tipologia' => $this->id_tipologia])
            ->andFilterWhere(['id_sottotipologia' => $this->id_sottotipologia])
            ->andFilterWhere(['stato' => $this->stato])
            ->andFilterWhere(['num_elenco_territoriale' => $this->num_elenco_territoriale]);

        return $dataProvider;

    }

    public function searchByEvento($id_evento, $params, $paginate = true)
    {
        $query = ViewVolontariAttivazioni::find()->where(['id_evento'=>$id_evento]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        

        $query
            ->andFilterWhere(['ilike', 'localita', $this->localita])
            ->andFilterWhere(['ilike', 'targa', $this->targa])
            ->andFilterWhere(['ilike', 'modello', $this->modello])
            ->andFilterWhere(['ilike', 'nome', $this->nome])
            ->andFilterWhere(['ilike', 'cognome', $this->cognome])
            ->andFilterWhere(['ilike', 'codfiscale', $this->codfiscale])
            ->andFilterWhere(['comune' => $this->comune])
            ->andFilterWhere(['provincia' => $this->provincia])
            ->andFilterWhere(['ilike', 'denominazione', $this->denominazione])
            ->andFilterWhere(['tipologia' => $this->tipologia])
            ->andFilterWhere(['sottotipologia' => $this->sottotipologia])
            ->andFilterWhere(['protocollo_evento' => $this->protocollo_evento])
            ->andFilterWhere(['protocollo_fronte' => $this->protocollo_fronte])
            ->andFilterWhere(['id_fronte' => $this->id_fronte])
            ->andFilterWhere(['id_tipologia' => $this->id_tipologia])
            ->andFilterWhere(['id_sottotipologia' => $this->id_sottotipologia])
            ->andFilterWhere(['stato' => $this->stato])
            ->andFilterWhere(['num_elenco_territoriale' => $this->num_elenco_territoriale]);

        return $dataProvider;

    }

    public function searchByFronte($id_evento, $params, $paginate = true)
    {
        $query = ViewVolontariAttivazioni::find()->where(['id_fronte'=>$id_evento]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        

        $query
            ->andFilterWhere(['ilike', 'localita', $this->localita])
            ->andFilterWhere(['ilike', 'targa', $this->targa])
            ->andFilterWhere(['ilike', 'modello', $this->modello])
            ->andFilterWhere(['ilike', 'nome', $this->nome])
            ->andFilterWhere(['ilike', 'cognome', $this->cognome])
            ->andFilterWhere(['ilike', 'codfiscale', $this->codfiscale])
            ->andFilterWhere(['comune' => $this->comune])
            ->andFilterWhere(['provincia' => $this->provincia])
            ->andFilterWhere(['ilike', 'denominazione', $this->denominazione])
            ->andFilterWhere(['tipologia' => $this->tipologia])
            ->andFilterWhere(['sottotipologia' => $this->sottotipologia])
            ->andFilterWhere(['protocollo_evento' => $this->protocollo_evento])
            ->andFilterWhere(['protocollo_fronte' => $this->protocollo_fronte])
            ->andFilterWhere(['id_fronte' => $this->id_fronte])
            ->andFilterWhere(['id_tipologia' => $this->id_tipologia])
            ->andFilterWhere(['id_sottotipologia' => $this->id_sottotipologia])
            ->andFilterWhere(['stato' => $this->stato])
            ->andFilterWhere(['num_elenco_territoriale' => $this->num_elenco_territoriale]);

        return $dataProvider;

    }

    
}
