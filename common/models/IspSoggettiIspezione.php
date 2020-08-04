<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "isp_soggetti_ispezione".
 *
 * @property integer $id
 * @property string $nome
 *
 * @property ConIspezioneSoggetti[] $conIspezioneSoggettis
 * @property IspIspezione[] $idispeziones
 */
class IspSoggettiIspezione extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'isp_soggetti_ispezione';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nome'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConIspezioneSoggettis()
    {
        return $this->hasMany(ConIspezioneSoggetti::className(), ['idsoggetto' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdispeziones()
    {
        return $this->hasMany(IspIspezione::className(), ['id' => 'idispezione'])->viaTable('con_ispezione_soggetti', ['idsoggetto' => 'id']);
    }
}
