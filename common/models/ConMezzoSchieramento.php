<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

use common\models\UtlAutomezzo;
use yii\data\ActiveDataProvider;
use common\models\VolSchieramento;

class ConMezzoSchieramento extends \yii\db\ActiveRecord
{
    public $targa, $idtipo;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_mezzo_schieramento';
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
            [['id_utl_automezzo', 'id_vol_schieramento'], 'required'],
            [['id_utl_automezzo', 'id_vol_schieramento'], 'integer'],
            [['date_from','date_to'],'safe'],

            [['targa'], 'string'],
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
            'id_utl_automezzo' => 'Mezzo',
            'created_at' => 'Creazione',
            'updated_at' => 'Ultimo aggiornamento'
        ];
    }

    public function extraFields() {
        return ['automezzo', 'schieramento'];
    }

    public function getAutomezzo() {
        return $this->hasOne(UtlAutomezzo::className(), ['id'=>'id_utl_automezzo']);
    }

    public function getSchieramento() {
        return $this->hasOne(VolSchieramento::className(), ['id'=>'id_vol_schieramento']);   
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['search'] = [
            'targa','idtipo','date_from','date_to'
        ];
        return $scenarios;
    }

    public function search($params) {
        
        $model = new ConMezzoSchieramento();
        $model->scenario = 'search';
        $query = $model->find()->joinWith(['automezzo', 'automezzo.tipo']);
       

        
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


        $query->andFilterWhere(['=', 'utl_automezzo.targa', $this->targa]);
        $query->andFilterWhere(['=', 'utl_automezzo.idtipo', $this->idtipo]);
        
        
        

        return $dataProvider;
    }


}
