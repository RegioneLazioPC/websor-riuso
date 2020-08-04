<?php

namespace common\models;

use Yii;


class IspIspezione extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'isp_ispezione';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idoperatore', 'idtipologia', 'idcomune'], 'integer'],
            [['lat', 'lon'], 'number'],
            [['tipo_attivita', 'map'], 'string'],
            [['indirizzo'], 'string', 'max' => 255],
            [['idoperatore'], 'exist', 'skipOnError' => true, 'targetClass' => UtlOperatorePc::className(), 'targetAttribute' => ['idoperatore' => 'id']],
            [['idtipologia'], 'exist', 'skipOnError' => true, 'targetClass' => UtlTipologia::className(), 'targetAttribute' => ['idtipologia' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idoperatore' => 'Idoperatore',
            'idtipologia' => 'Idtipologia',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'idcomune' => 'Idcomune',
            'indirizzo' => 'Indirizzo',
            'tipo_attivita' => 'Tipo Attivita',
            'map' => 'Mappa'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReport()
    {
        //return $this->hasMany(IspReport::className(), ['idispezione' => 'id']);
        return $this->hasOne(IspReport::className(), ['idispezione' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConIspezioneSoggettis()
    {
        return $this->hasMany(ConIspezioneSoggetti::className(), ['idispezione' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdsoggettos()
    {
        return $this->hasMany(IspSoggettiIspezione::className(), ['id' => 'idsoggetto'])->viaTable('con_ispezione_soggetti', ['idispezione' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperatore()
    {
        return $this->hasOne(UtlOperatorePc::className(), ['id' => 'idoperatore']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipologia()
    {
        return $this->hasOne(UtlTipologia::className(), ['id' => 'idtipologia']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComune()
    {
        return $this->hasOne(LocComune::className(), ['id' => 'idcomune']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIspIspezioneIspSoggettiIspeziones()
    {
        return $this->hasMany(IspIspezioneIspSoggettiIspezione::className(), ['isp_ispezione_id' => 'id']);
    }
}
