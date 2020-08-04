<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AuditTrail;
use yii\db\Expression;

/**
 * AuditTrailSearch represents the model behind the search form of `common\models\AuditTrail`.
 */
class AuditTrailSearch extends AuditTrail
{

    public $nome, $num_protocollo;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'model_id'], 'integer'],
            [['model', 'action', 'field', 'action', 'nome', 'num_protocollo'], 'string'],
            [['stamp'], 'date', 'format' => 'php:Y-m-d H:i' ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $model)
    {
        $query = AuditTrail::find()->orderBy([
            'stamp' => SORT_DESC      
        ])
        ->leftJoin('"user" ON "tbl_audit_trail"."user_id" = "user"."id"::TEXT ')
        ->leftJoin('"utl_operatore_pc" ON "user"."id" = "utl_operatore_pc"."iduser"')
        ->leftJoin('"utl_anagrafica" ON "utl_operatore_pc"."id_anagrafica" = "utl_anagrafica"."id" ');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        $this->load($params);
        // per il model su cui cercare di default
        $this->model = $model;

        switch($model) {
            case 'common\models\UtlSegnalazione':
            $query->leftJoin('"utl_segnalazione" as related_model on "related_model"."id" = "tbl_audit_trail"."model_id"::INT');
            $dataProvider->sort->attributes['num_protocollo'] = [
                'asc' => ['related_model.num_protocollo' => SORT_ASC],
                'desc' => ['related_model.num_protocollo' => SORT_DESC],
            ];
            break;
            case 'common\models\UtlEvento':
            $query->leftJoin('"utl_evento" as related_model on "related_model"."id" = "tbl_audit_trail"."model_id"::INT');
            $dataProvider->sort->attributes['num_protocollo'] = [
                'asc' => ['related_model.num_protocollo' => SORT_ASC],
                'desc' => ['related_model.num_protocollo' => SORT_DESC],
            ];
            break;
            case 'common\models\UtlIngaggio':
            $query
                ->leftJoin('"utl_ingaggio" on "utl_ingaggio"."id" = "tbl_audit_trail"."model_id"::INT')
                ->leftJoin('"utl_evento" as related_model on "related_model"."id" = "utl_ingaggio"."idevento"')
                ->andWhere('"tbl_audit_trail"."model_id" is not null')
                ->andWhere('"tbl_audit_trail"."model" = \'common\models\UtlIngaggio\'');;
            $dataProvider->sort->attributes['num_protocollo'] = [
                'asc' => ['related_model.num_protocollo' => SORT_ASC],
                'desc' => ['related_model.num_protocollo' => SORT_DESC],
            ];
            break;
            case 'common\models\RichiestaCanadair':
            $query
                ->leftJoin('"richiesta_canadair" on "richiesta_canadair"."id" = "tbl_audit_trail"."model_id"::INT')
                ->leftJoin('"utl_evento" as related_model on "related_model"."id" = "richiesta_canadair"."idevento"')
                ->andWhere('"tbl_audit_trail"."model_id" is not null')
                ->andWhere('"tbl_audit_trail"."model" = \'common\models\RichiestaCanadair\'');;
            $dataProvider->sort->attributes['num_protocollo'] = [
                'asc' => ['related_model.num_protocollo' => SORT_ASC],
                'desc' => ['related_model.num_protocollo' => SORT_DESC],
            ];
            break;
            case 'common\models\RichiestaDos':
            $query
                ->leftJoin('"richiesta_dos" on "richiesta_dos"."id" = "tbl_audit_trail"."model_id"::INT')
                ->leftJoin('"utl_evento" as related_model on "related_model"."id" = "richiesta_dos"."idevento"')
                ->andWhere('"tbl_audit_trail"."model_id" is not null')
                ->andWhere('"tbl_audit_trail"."model" = \'common\models\RichiestaDos\'');;
            $dataProvider->sort->attributes['num_protocollo'] = [
                'asc' => ['related_model.num_protocollo' => SORT_ASC],
                'desc' => ['related_model.num_protocollo' => SORT_DESC],
            ];
            break;
            case 'common\models\RichiestaElicottero':
            $query
                ->leftJoin('"richiesta_elicottero" on "richiesta_elicottero"."id" = "tbl_audit_trail"."model_id"::INT')
                ->leftJoin('"utl_evento" as related_model on "related_model"."id" = "richiesta_elicottero"."idevento"')
                ->andWhere('"tbl_audit_trail"."model_id" is not null')
                ->andWhere('"tbl_audit_trail"."model" = \'common\models\RichiestaElicottero\'');;
            $dataProvider->sort->attributes['num_protocollo'] = [
                'asc' => ['related_model.num_protocollo' => SORT_ASC],
                'desc' => ['related_model.num_protocollo' => SORT_DESC],
            ];
            break;
        }

        if (!$this->validate()) {

            
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'model' => $this->model,
            'model_id' => $this->model_id,
            'stamp' => $this->stamp,
            'action' => $this->action,
            'field' => $this->field
        ]);

        if ( !empty( $this->nome ) ) {
            $query->andWhere( ['ilike', new Expression('CONCAT(utl_anagrafica.cognome, \' \', utl_anagrafica.nome)'), $this->nome ] );
        }

        $dataProvider->sort->attributes['nome'] = [
            'asc' => ['utl_anagrafica.cognome' => SORT_ASC],
            'desc' => ['utl_anagrafica.cognome' => SORT_DESC],
        ];        

        if ( !empty( $this->num_protocollo ) ) {
            $query->andWhere( ['=', 'related_model.num_protocollo', $this->num_protocollo ] );
        }

        return $dataProvider;
    }
}
