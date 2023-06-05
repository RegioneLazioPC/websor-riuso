<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

use common\models\UtlAttrezzatura;
use common\models\VolSchieramento;
use yii\data\ActiveDataProvider;

class ConAttrezzaturaSchieramento extends \yii\db\ActiveRecord
{
    public $modello, $idtipo;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_attrezzatura_schieramento';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ],
            TimestampBehavior::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['created_at', 'updated_at'], 'integer'],
            [['id_utl_attrezzatura', 'id_vol_schieramento'], 'required'],
            [['id_utl_attrezzatura', 'id_vol_schieramento'], 'integer'],

            [['modello'], 'string'],
            [['idtipo'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_vol_schieramento' => 'Schieramento',
            'id_utl_attrezzatura' => 'Attrezzatura',
            'created_at' => 'Creazione',
            'updated_at' => 'Ultimo aggiornamento'
        ];
    }

    public function extraFields() {
        return ['attrezzatura', 'schieramento'];
    }

    public function getAttrezzatura() {
        return $this->hasOne(UtlAttrezzatura::className(), ['id'=>'id_utl_attrezzatura']);
    }

    public function getSchieramento() {
        return $this->hasOne(VolSchieramento::className(), ['id'=>'id_vol_schieramento']);   
    }


    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['search'] = [
            'modello','idtipo','date_from','date_to'
        ];
        return $scenarios;
    }

    public function search($params) {
        
        $model = new ConAttrezzaturaSchieramento();
        $model->scenario = 'search';
        $query = $model->find()->joinWith(['attrezzatura', 'attrezzatura.tipo']);
       

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $this->load($params);

        

        if (!$this->validate()) {
            throw new \Exception(json_encode($this->getErrors()), 1);
            
            return $dataProvider;
        }
        
        
        if(!empty($this->date_from)) $query->andFilterWhere(['>=', 'date_from', $this->date_from . " 00:00:01"]);
        if(!empty($this->date_to)) $query->andFilterWhere(['<=', 'date_to', $this->date_to . " 23:59:59"]);


        $query->andFilterWhere(['=', 'utl_attrezzatura.modello', $this->modello]);
        $query->andFilterWhere(['=', 'utl_attrezzatura.idtipo', $this->idtipo]);
        
        
        

        return $dataProvider;
    }
}
