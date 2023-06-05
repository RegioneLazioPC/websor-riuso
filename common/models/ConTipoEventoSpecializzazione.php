<?php

namespace common\models;

use Yii;

use common\models\UtlTipologia;
use common\models\TblSezioneSpecialistica;
use yii\data\ActiveDataProvider;
use yii\behaviors\TimestampBehavior;
class ConTipoEventoSpecializzazione extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ]          
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_tipo_evento_specializzazione';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_utl_tipologia', 'id_tbl_sezione_specialistica'], 'integer'],
            [['id_utl_tipologia'], 'exist', 'skipOnError' => true, 'targetClass' => UtlTipologia::className(), 'targetAttribute' => ['id_utl_tipologia' => 'id']],
            [['id_tbl_sezione_specialistica'], 'exist', 'skipOnError' => true, 'targetClass' => TblSezioneSpecialistica::className(), 'targetAttribute' => ['id_tbl_sezione_specialistica' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_utl_tipologia' => 'Tipologia evento',
            'id_tbl_sezione_specialistica' => 'Specializzazione',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecializzazione()
    {
        return $this->hasOne(TblSezioneSpecialistica::className(), ['id' => 'id_tbl_sezione_specialistica']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipologia()
    {
        return $this->hasOne(UtlTipologia::className(), ['id' => 'id_utl_tipologia']);
    }

    public function search($params)
    {
        $query = ConTipoEventoSpecializzazione::find()->joinWith(['tipologia','specializzazione']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'id_tbl_sezione_specialistica' => $this->id_tbl_sezione_specialistica,
            'id_utl_tipologia' => $this->id_utl_tipologia
        ]);

        $dataProvider->sort->attributes['id_utl_tipologia'] = [
            'asc'  => ['utl_tipologia.tipologia' => SORT_ASC],
            'desc' => ['utl_tipologia.tipologia' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['id_tbl_sezione_specialistica'] = [
            'asc'  => ['tbl_sezione_specialistica.descrizione' => SORT_ASC],
            'desc' => ['tbl_sezione_specialistica.descrizione' => SORT_DESC],
        ];

        
        return $dataProvider;
    }
}
