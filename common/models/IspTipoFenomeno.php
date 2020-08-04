<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "isp_tipo_fenomeno".
 *
 * @property integer $id
 * @property integer $idparent
 * @property integer $order
 * @property string $voce
 *
 * @property IspReport[] $ispReports
 */
class IspTipoFenomeno extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'isp_tipo_fenomeno';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idparent', 'order'], 'integer'],
            [['voce'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idparent' => 'Idparent',
            'order' => 'Order',
            'voce' => 'Voce',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIspReports()
    {
        return $this->hasMany(IspReport::className(), ['idtipo_fenomeno' => 'id']);
    }
}
