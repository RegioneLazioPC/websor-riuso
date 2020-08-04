<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "utl_extra_utente".
 *
 * @property integer $id
 * @property string $voce
 * @property integer $parent_id
 */
class UtlExtraUtente extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_extra_utente';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
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
            'voce' => 'Voce',
            'parent_id' => 'Parent ID',
        ];
    }
}
