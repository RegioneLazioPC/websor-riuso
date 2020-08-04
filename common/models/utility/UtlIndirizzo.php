<?php

namespace common\models\utility;

use Yii;
use yii\behaviors\TimestampBehavior;

use common\models\LocComune;
/**
 * Model per la tabella "utl_indirizzo".
 *
 * @property int $id
 * @property string $indirizzo
 * @property string $civico
 * @property string $cap
 * @property int $id_comune
 * @property string $note
 * @property int $created_at
 * @property int $updated_at
 *
 * 
 */
class UtlIndirizzo extends \yii\db\ActiveRecord
{
    const SCENARIO_ADD_RUBRICA = 'rubrica';
    

    public $full_address;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'utl_indirizzo';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    
    public function fields()
    {
        return array_merge(parent::fields(), [
            'full_address' => function ($model) {
                $comune = @$model->comune->comune;
                $sigla = @$model->comune->provincia->sigla;
                return $model->indirizzo . " " . $model->civico . ", " . $model->cap . " " . $comune . " (" . $sigla . ")";
            }
        ]);
    }

    public function extraFields()
    {
        return [
            'comune'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_comune', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['id_comune', 'created_at', 'updated_at'], 'integer'],
            [['note'], 'string'],
            [['indirizzo', 'civico', 'cap', 'id_sync'], 'string', 'max' => 255],
            [['indirizzo','civico','id_comune','cap'],'required', 'on' => self::SCENARIO_ADD_RUBRICA],
            [['id_comune'], 'exist', 'skipOnError' => true, 'targetClass' => LocComune::className(), 'targetAttribute' => ['id_comune' => 'id']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_ADD_RUBRICA] = [
            'indirizzo','civico','id_comune','cap'
        ];
        
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'indirizzo' => 'Indirizzo',
            'civico' => 'Civico',
            'cap' => 'Cap',
            'id_comune' => 'Id Comune',
            'note' => 'Note',
            'id_sync' => 'Sync',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    
}
