<?php

namespace common\models;

use Yii;
use common\models\ConMasInvioContact;
use yii\data\ActiveDataProvider;
use yii\base\Model;
/**
 * This is the model class for table "con_mas_invio_contact".
 *
 * @property int $id
 * @property int $id_invio
 * @property int $id_rubrica_contatto
 * @property int $tipo_rubrica_contatto
 * @property string $channel
 * @property string $valore_rubrica_contatto
 *
 * @property MasSingleSend[] $masSingleSends
 */
class ConMasInvioContactSearch extends ConMasInvioContact
{
    public $group;
    public $sent;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_invio', 'id_rubrica_contatto', 'tipo_rubrica_contatto'], 'default', 'value' => null],
            [['id_invio', 'id_rubrica_contatto', 'tipo_rubrica_contatto'], 'integer'],
            [['channel'], 'string', 'max' => 20],
            [['valore_riferimento'], 'string'],
            [['group', 'sent'], 'safe'],
            [['valore_rubrica_contatto'], 'string', 'max' => 255],
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
    public function search($params)
    {
        $query = ConMasInvioContact::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $this->load($params);

        

        if (!$this->validate()) {
            
            return $dataProvider;
        }
        
        // grid filtering conditions
        $query->andFilterWhere([
            'id_invio' => $this->id_invio,
            'id_rubrica_contatto' => $this->id_rubrica_contatto,
            'tipo_rubrica_contatto' => $this->tipo_rubrica_contatto,
            'channel'=>$this->channel
        ]);

        $query->andFilterWhere(['ilike', 'valore_rubrica_contatto', $this->valore_rubrica_contatto]);
        $query->andFilterWhere(['ilike', 'valore_riferimento', $this->valore_riferimento]);
        
        if(!empty($this->group)) {
            $query->from(['t' => '(SELECT distinct on (id_rubrica_contatto, tipo_rubrica_contatto, id_invio) * FROM con_mas_invio_contact)']);


            if(!empty($this->sent)) {
                if($this->sent == 1) {
                    $query->andWhere('(SELECT count(id) FROM mas_single_send WHERE 
                        mas_single_send.id_rubrica_contatto = t.id_rubrica_contatto
                        AND mas_single_send.tipo_rubrica_contatto = t.tipo_rubrica_contatto
                        AND status = ' . \common\models\MasMessage::STATUS_SEND . ') > 0');
                } else {

                    $query->andWhere('(SELECT count(id) FROM mas_single_send WHERE 
                        mas_single_send.id_rubrica_contatto = t.id_rubrica_contatto
                        AND mas_single_send.tipo_rubrica_contatto = t.tipo_rubrica_contatto
                        AND status = ' . \common\models\MasMessage::STATUS_SEND . ') = 0');
                }
            }

            
        } else {
            if(!empty($this->sent)) {
                if($this->sent == 1) {
                    $query->andWhere('(SELECT count(id) FROM mas_single_send WHERE 
                        mas_single_send.id_con_mas_invio_contact = con_mas_invio_contact.id
                        AND status = ' . \common\models\MasMessage::STATUS_SEND . ') > 0');
                } else {

                    $query->andWhere('(SELECT count(id) FROM mas_single_send WHERE 
                        mas_single_send.id_con_mas_invio_contact = con_mas_invio_contact.id
                        AND status = ' . \common\models\MasMessage::STATUS_SEND . ') = 0');
                }
            }
        }

        

        return $dataProvider;
    }
}
