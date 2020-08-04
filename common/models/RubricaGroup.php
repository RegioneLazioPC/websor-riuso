<?php

namespace common\models;

use Yii;
use common\models\ViewRubrica;
/**
 * This is the model class for table "rubrica_group".
 *
 * @property int $id
 * @property string $name
 */
class RubricaGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rubrica_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    public function getContatto() {
        return $this->hasMany(ViewRubrica::className(), [
            'id_riferimento'=>'id_rubrica_contatto', 
            'tipo_riferimento'=>'tipo_rubrica_contatto'])
        ->viaTable('con_rubrica_group_contact', ['id_group'=>'id']);
    }
}
