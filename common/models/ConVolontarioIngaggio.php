<?php

namespace common\models;

use Yii;


class ConVolontarioIngaggio extends \yii\db\ActiveRecord
{

    public $nome;
    public $cognome;
    public $codfiscale;
    public $email;
    public $telefono;

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior',
                'ignored' => ['datore_di_lavoro'],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'con_volontario_ingaggio';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_volontario', 'id_ingaggio'], 'default', 'value' => null],
            [['id_volontario', 'id_ingaggio'], 'integer'],
            [['refund'], 'boolean'],
            [['nome','cognome','codfiscale', 'email', 'telefono'], 'string'],
            [['id_ingaggio'], 'exist', 'skipOnError' => true, 'targetClass' => UtlIngaggio::className(), 'targetAttribute' => ['id_ingaggio' => 'id']],
            [['id_volontario'], 'exist', 'skipOnError' => true, 'targetClass' => VolVolontario::className(), 'targetAttribute' => ['id_volontario' => 'id']],
            [['datore_di_lavoro'], 'validateDatoreDiLavoro']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_volontario' => 'Volontario',
            'id_ingaggio' => 'Ingaggio',
            'refund' => 'Rimborso',
            'nome'=>'Nome',
            'cognome'=>'Cognome',
            'codfiscale' => 'Codice fiscale',
            'email' => 'Email',
            'telefono' => 'Telefono'
        ];
    }

    public function validateDatoreDiLavoro($attribute_name, $params){
        
        $keys = [   
                    'autonomo',
                    'denominazione',
                    'from',
                    'to',
                    'cfpiva',
                    'email',
                    'pec',
                    'tel',
                    'fax',
                    'via',
                    'civico',
                    'cap',
                    'pr',
                    'comune',
                ];

        $required_keys = [   
                    'denominazione',
                    'cfpiva',
                    'via',
                    'civico',
                    'cap',
                    'pr',
                    'comune',
                ];

        if(!is_array($this->$attribute_name)) {
            $this->addError($attribute_name, "Formato non valido");
        }

        foreach ($this->$attribute_name as $key => $value) {
            if(!in_array($key, $keys)) {
                $this->addError($attribute_name, "Formato non valido");
            }
        }

        foreach ($required_keys as $key) {
            if(!array_key_exists($key, $this->$attribute_name )) {
                $this->addError($attribute_name, "Formato ".$key." non valido");
            }
        }

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIngaggio()
    {
        return $this->hasOne(UtlIngaggio::className(), ['id' => 'id_ingaggio']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVolontario()
    {
        return $this->hasOne(VolVolontario::className(), ['id' => 'id_volontario']);
    }
}
