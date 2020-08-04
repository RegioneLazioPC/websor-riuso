<?php

namespace common\models\utility;

use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * Model per la tabella "utl_contatto".
 *
 * @property int $id
 * @property int $type
 * @property string $contatto
 * @property string $note
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ConContattoAnagrafica[] $conContattoAnagraficas
 * @property ConVolontarioContatto[] $conVolontarioContattos
 */
class UtlContatto extends \yii\db\ActiveRecord
{
    const USE_TYPE_MESSAGE = 0;
    const USE_TYPE_ENGAGE = 1;
    const USE_TYPE_ALERT = 2;

    const TYPE_DEVICE = 6;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'utl_contatto';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()            
        ];
    }

    public function fields() {
        return array_merge(parent::fields(), [
            'tipo' => function($model) {
                switch($model->type){
                    case 0: return "Email"; break;
                    case 1: return "Pec"; break;
                    case 2: return "Telefono"; break;
                    case 3: return "Fax"; break;
                    case 4: return "Tel h24"; break;
                    case 5: return "Fax h24"; break;
                    case 6: return "Device"; break;
                    case 7: return "Sito web"; break;
                }
            }
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['use_type'], 'default', 'value' => 0],
            [['vendor'], 'string'],
            [['vendor'], 'default', 'value' => 'ios'],
            [['check_mobile', 'check_predefinito', 'type', 'created_at', 'updated_at', 'use_type'], 'integer'],
            [['type', 'contatto'], 'required'],
            [['note', 'id_sync'], 'string'],
            [['contatto'], 'validateContatto'],
        ];
    }

    public function validateContatto($attribute_name, $params){

        $contatto = $this->$attribute_name;
        
        switch( $this->type ) {
            case 0: case 1:
                $user   = '[a-zA-Z0-9_\-\.\+\^!#\$%&*+\/\=\?\`\|\{\}~\']+';
                $domain = '(?:(?:[a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.?)+';
                $ipv4   = '[0-9]{1,3}(\.[0-9]{1,3}){3}';
                $ipv6   = '[0-9a-fA-F]{1,4}(\:[0-9a-fA-F]{1,4}){7}';

                if( !preg_match("/^$user@($domain|(\[($ipv4|$ipv6)\]))$/", $contatto) ) {
                    $this->addError("contatto", "Indirizzo email non valido");
                }
                
            break;
            case 6:

            break;
            case 7:
                if(!filter_var($contatto, FILTER_VALIDATE_URL)) $this->addError("contatto", "Indirizzo sito web non valido");
            break;
            case 2: case 3: case 4: case 5:  
                $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
                try{
                    $number = $phoneUtil->parse($contatto, "IT");
                    if ( !$phoneUtil->isValidNumber($number) ) $this->addError("contatto", "Telefono non valido");
                } catch(\Exception $e) {
                    $this->addError("contatto", "Telefono non valido");
                }
            break;           
            default:
                $this->addError("contatto", "Contatto non valido");
            break; 
        }
    }

    /**
     * Anagrafica
     * @return [type] [description]
     */
    public function getAnagrafica() {
        return $this->hasMany(\common\models\UtlAnagrafica::className(), ['id' => 'id_anagrafica'])
        ->viaTable('con_anagrafica_contatto', ['id_contatto' => 'id']);
    }

    public function beforeSave( $insert )
    {  
        /**
         * Formatto il telefono per il successivo inserimento in rubrica
         */
        if($this->type != 0 && $this->type != 1 && $this->type != 6 && $this->type != 7) {
            $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
            $number = $phoneUtil->parse($this->contatto, "IT");
            $this->contatto = $phoneUtil->format($number, \libphonenumber\PhoneNumberFormat::INTERNATIONAL); // E164 (questa altra formattazione mi servirà per l'utilizzo nella rubrica)
        }
        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type', // 0=>email,1=pec,2=telefono,3=fax
            'contatto' => 'Contatto',
            'note' => 'Note',
            'id_sync' => 'Sync',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @see also ViewRubrica
     * @return [type] [description]
     */
    public function tipo() {
        switch ($this->type) {
            case 0: return "Email"; break;
            case 1: return "Pec"; break;
            case 2: return "Telefono"; break;
            case 3: return "Fax"; break;
            case 4: return "Tel h24"; break;
            case 5: return "Fax h24"; break;
            case 6: return "Device"; break;
            default:
                return "";
                break;
        }
    }

    
}
