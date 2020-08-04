<?php

namespace common\models;

use Yii;
use common\models\RubricaGroup;
use common\models\TblSezioneSpecialistica;
/**
 * This is the model class for table "view_rubrica".
 *
 * @property string $valore_contatto
 * @property string $valore_riferimento
 * @property int $tipo_contatto
 * @property string $tipologia_riferimento
 * @property double $lat
 * @property double $lon
 * @property int $id_riferimento
 * @property string $tipo_riferimento
 * @property int $id_anagrafica
 * @property string $indirizzo
 * @property string $comune
 * @property string $provincia
 */
class ViewRubrica extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_rubrica';
    }

    public static function primaryKey()
    {
        return ["id_contatto"];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['check_predefinito', 'check_mobile'], 'integer'],
            [['note'], 'string'],
            [['valore_riferimento', 'tipologia_riferimento', 'tipo_riferimento', 'indirizzo'], 'string'],
            [['tipo_contatto', 'id_riferimento', 'id_anagrafica'], 'default', 'value' => null],
            [['tipo_contatto', 'id_riferimento', 'id_anagrafica'], 'integer'],
            [['lat', 'lon'], 'number'],
            [['n_records'], 'integer'],
            [['identificativo'], 'string'],
            [['ext_id', 'everbridge_identifier'], 'string'],
            [['valore_contatto', 'comune'], 'string', 'max' => 255],
            [['use_type'], 'integer'],
            [['id_contatto','contatto_type'], 'safe'],
            [['provincia'], 'string', 'max' => 2],
        ];
    }

    public function tipo() {
        switch ($this->tipo_contatto) {
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

    public static function getTipi() {
        return [
            0=>"Email",
            1=>"Pec",
            2=>"Telefono",
            3=>"Fax",
            4=>"Tel h24",
            5=>"Fax h24",
            6=>"Device",
        ];
    }

    public static function getTipiRiferimento() {
        return [
            "organizzazione"=> "organizzazione",
            "ente" => "ente",
            "struttura" => "struttura",
            "operatore pc" => "operatore pc",
            "volontario"=> "volontario",
            "prefettura"=> "prefettura",
            "comunita' montana"=> "comunita' montana",
            "comune"=> "comune",
            "mas_rubrica"=>"mas_rubrica"
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'valore_contatto' => 'Valore Contatto',
            'valore_riferimento' => 'Valore Riferimento',
            'tipo_contatto' => 'Tipo Contatto',
            'tipologia_riferimento' => 'Tipologia Riferimento',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'id_riferimento' => 'Id Riferimento',
            'tipo_riferimento' => 'Tipo Riferimento',
            'id_anagrafica' => 'Id Anagrafica',
            'indirizzo' => 'Indirizzo',
            'comune' => 'Comune',
            'provincia' => 'Provincia',
        ];
    }

    public function getGruppo() {
        return $this->hasMany(RubricaGroup::className(), [
            'id'=>'id_group'])
        ->viaTable('con_rubrica_group_contact', [
            'id_rubrica_contatto'=>'id_riferimento', 
            'tipo_rubrica_contatto'=>'tipo_riferimento'
        ]);
    }

    public function getSpecializzazioni() {
        return $this->hasMany(TblSezioneSpecialistica::className(), [ 'id' => 'id_sezione_specialistica'])
        ->viaTable('con_organizzazione_sezione_specialistica', ['id_organizzazione' => 'id_riferimento']);
    }

}
