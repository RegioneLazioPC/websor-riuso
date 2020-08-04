<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "loc_provincia".
 *
 * @property integer $id
 * @property integer $id_regione
 * @property string $provincia
 * @property string $sigla
 */
class LocProvincia extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loc_provincia';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_regione', 'provincia', 'sigla'], 'required'],
            [['id', 'id_regione'], 'integer'],
            [['provincia'], 'string'],
            [['sigla'], 'string', 'max' => 2]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_regione' => 'Id Regione',
            'provincia' => 'Provincia',
            'sigla' => 'Sigla',
        ];
    }
}
