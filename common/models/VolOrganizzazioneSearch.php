<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\VolOrganizzazione;

/**
 * VolOrganizzazioneSearch represents the model behind the search form about `common\models\VolOrganizzazione`.
 */
class VolOrganizzazioneSearch extends VolOrganizzazione
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_tipo_organizzazione', 'num_albo_regionale', 'num_albo_provinciale', 'num_albo_nazionale', 'num_assicurazione', 'ref_id', 'stato_iscrizione'], 'integer'],
            [['denominazione', 'codicefiscale', 'partita_iva', 'tipo_albo_regionale', 'data_albo_regionale', 'societa_assicurazione', 'data_scadenza_assicurazione', 'note',
            'provincia', 'sezione_specialistica','comune','zone_allerta'], 'safe'],
            [['update_zona_allerta_strategy'], 'integer']
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
        $query = VolOrganizzazione::find()->joinWith(['tipoOrganizzazione'])
        ->orderBy(['ref_id'=>SORT_ASC]);
        $query->addSelect([
            'vol_organizzazione.*',
            'string_agg( distinct c.comune, \', \' ) AS comune',
            'string_agg( distinct c.provincia_sigla, \', \' ) AS provincia',
            'string_agg( distinct sez.descrizione, \', \' ) AS sezione_specialistica'
        ])
        ->joinWith([
            'volSedes s',
            'tipoOrganizzazione vt',
            'volSedes.locComune c',
            'sezioneSpecialistica sez'
        ])
        ->groupBy([
            'vol_organizzazione.id',
            'vol_organizzazione.denominazione',
            'vt.tipologia',
            'vol_organizzazione.codicefiscale',
            'vol_organizzazione.partita_iva',
            'vol_organizzazione.nome_responsabile',
            'vol_organizzazione.cf_rappresentante_legale',
            'vol_organizzazione.tel_responsabile',
            'vol_organizzazione.nome_referente',
            'vol_organizzazione.cf_referente',
            'vol_organizzazione.tel_referente',
            'vol_organizzazione.fax_referente',
            'vol_organizzazione.email_referente',
            'vol_organizzazione.data_costituzione'
        ]);

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'ref_id' => $this->ref_id,
            'id_tipo_organizzazione' => $this->id_tipo_organizzazione,
            'num_albo_regionale' => $this->num_albo_regionale,
            'data_albo_regionale' => $this->data_albo_regionale,
            'num_albo_provinciale' => $this->num_albo_provinciale,
            'num_albo_nazionale' => $this->num_albo_nazionale,
            'num_assicurazione' => $this->num_assicurazione,
            'data_scadenza_assicurazione' => $this->data_scadenza_assicurazione,
        ]);

        $query->andFilterWhere(['ilike', 'denominazione', $this->denominazione])
            ->andFilterWhere(['ilike', 'codicefiscale', $this->codicefiscale])
            ->andFilterWhere(['ilike', 'partita_iva', $this->partita_iva])
            ->andFilterWhere(['ilike', 'tipo_albo_regionale', $this->tipo_albo_regionale])
            ->andFilterWhere(['ilike', 'societa_assicurazione', $this->societa_assicurazione])
            ->andFilterWhere(['ilike', 'note', $this->note]);

        if(!empty($this->provincia)) {
            $query->andFilterWhere(['in','c.provincia_sigla',$this->provincia]);
        }

        if(!empty($this->comune)) {
            $query->andFilterWhere(['in', 'c.id', $this->comune]);
        }

        if(!empty($this->stato_iscrizione)) {
            if($this->stato_iscrizione == VolOrganizzazione::STATO_ATTIVA) {
                $query->andFilterWhere([ 'vol_organizzazione.stato_iscrizione' => $this->stato_iscrizione ]);
            } else {
                $query->andFilterWhere(['!=', 'vol_organizzazione.stato_iscrizione', VolOrganizzazione::STATO_ATTIVA]);
            }
        }

        if(!empty($this->sezione_specialistica)) {
            $query->andFilterWhere(['in','sez.id',$this->sezione_specialistica]);
        }

        if(!empty($this->zone_allerta)) {
            $cnd = ['and'];
            foreach ($this->zone_allerta as $zona) {
                $cnd[] = ['ilike', 'zone_allerta', $zona ];
            }
            $query->andFilterWhere($cnd);
        }

        if(!empty($this->update_zona_allerta_strategy)) {
            $query->andFilterWhere(['vol_organizzazione.update_zona_allerta_strategy'=>$this->update_zona_allerta_strategy]);
        }

        $dataProvider->sort->attributes['id_tipo_organizzazione'] = [
            'asc' => ['vol_tipo_organizzazione.tipologia' => SORT_ASC],
            'desc' => ['vol_tipo_organizzazione.tipologia' => SORT_DESC],
        ];

        return $dataProvider;
    }
}
