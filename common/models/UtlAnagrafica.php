<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "utl_anagrafica".
 *
 * @property int $id
 * @property string $nome
 * @property string $cognome
 * @property string $codfiscale
 * @property string $telefono
 * @property string $email
 * @property string $data_nascita
 * @property int $luogo_nascita
 * @property int $comune_residenza
 * @property string $matricola
 *
 * @property LocComune $luogoNascita
 * @property LocComune $comuneResidenza
 * @property UtlOperatorePc[] $utlOperatorePcs
 * @property UtlUtente[] $utlUtentes
 * @property VolVolontario[] $volVolontarios
 */
class UtlAnagrafica extends \yii\db\ActiveRecord
{
    const SCENARIO_UTL_UTENTE = 'utente';
    const SCENARIO_VOLONTARIO = 'volontario';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_anagrafica';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ]
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UTL_UTENTE] = ['nome',
                    'cognome',
                    'codfiscale',
                    'comune_residenza',
                    'data_nascita',
                    'luogo_nascita',
                    'telefono',
                    'email'];
        $scenarios[self::SCENARIO_VOLONTARIO] = [
            'nome', 'cognome', 'codfiscale'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data_nascita'], 'safe'],
            [['luogo_nascita', 'comune_residenza','indirizzo_residenza','cap_residenza','pec'], 'default', 'value' => null],
            [['comune_residenza'], 'integer'],
            [['nome', 'cognome', 'matricola','indirizzo_residenza','pec'], 'string', 'max' => 255],
            [['nome', 'cognome'], 'required'],
            [['luogo_nascita'], 'string'],
            [[ 'cap_residenza'], 'string', 'max' => 5],
            //[['codfiscale'], 'string', 'max' => 16],
            [['codfiscale'], 'validateCodiceFiscale'],
            [['telefono','id_sync'], 'string', 'max' => 100],
            [['email'], 'string', 'max' => 355],
            [['comune_residenza'], 'exist', 'skipOnError' => true, 'targetClass' => LocComune::className(), 'targetAttribute' => ['comune_residenza' => 'id']],
            [['codfiscale'], 'unique'],
            [
                [
                    'nome',
                    'cognome',
                    'codfiscale',
                    'comune_residenza',
                    'data_nascita',
                    'luogo_nascita',
                    'telefono',
                    'email',
                ], 'required', 'on'=>['utente']
            ],
            [
                [
                    'nome',
                    'cognome',
                    'codfiscale',
                ], 'required', 'on'=>[self::SCENARIO_VOLONTARIO]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome *',
            'cognome' => 'Cognome *',
            'codfiscale' => 'Codice fiscale',
            'telefono' => 'Telefono',
            'email' => 'Email',
            'pec' => 'Pec',
            'data_nascita' => 'Data Nascita',
            'luogo_nascita' => 'Luogo Nascita',
            'comune_residenza' => 'Comune Residenza',
            'matricola' => 'Matricola',
            'indirizzo_residenza' => 'Indirizzo',
            'cap_residenza' => 'Cap'
        ];
    }

    public function validateCodiceFiscale($attribute_name, $params){

        $cf = $this->$attribute_name;
        $message = "Codice fiscale non valido";

         if($cf=='')
          {
            $this->addError($attribute_name, $message);
            return;
          }

         if(strlen($cf)!= 16)
        {
            $this->addError($attribute_name, $message);
            return;
          }

         $cf=strtoupper($cf);
         if(!preg_match("/[A-Z0-9]+$/", $cf))
        return false;
         $s = 0;
         for($i=1; $i<=13; $i+=2){
        $c=$cf[$i];
        if('0'<=$c and $c<='9')
             $s+=ord($c)-ord('0');
        else
             $s+=ord($c)-ord('A');
         }

         for($i=0; $i<=14; $i+=2){
        $c=$cf[$i];
        switch($c){
                 case '0':  $s += 1;  break;
             case '1':  $s += 0;  break;
                 case '2':  $s += 5;  break;
             case '3':  $s += 7;  break;
             case '4':  $s += 9;  break;
             case '5':  $s += 13;  break;
             case '6':  $s += 15;  break;
             case '7':  $s += 17;  break;
             case '8':  $s += 19;  break;
             case '9':  $s += 21;  break;
             case 'A':  $s += 1;  break;
             case 'B':  $s += 0;  break;
             case 'C':  $s += 5;  break;
             case 'D':  $s += 7;  break;
             case 'E':  $s += 9;  break;
             case 'F':  $s += 13;  break;
             case 'G':  $s += 15;  break;
             case 'H':  $s += 17;  break;
             case 'I':  $s += 19;  break;
             case 'J':  $s += 21;  break;
             case 'K':  $s += 2;  break;
             case 'L':  $s += 4;  break;
             case 'M':  $s += 18;  break;
             case 'N':  $s += 20;  break;
             case 'O':  $s += 11;  break;
             case 'P':  $s += 3;  break;
                 case 'Q':  $s += 6;  break;
             case 'R':  $s += 8;  break;
             case 'S':  $s += 12;  break;
             case 'T':  $s += 14;  break;
             case 'U':  $s += 16;  break;
             case 'V':  $s += 10;  break;
             case 'W':  $s += 22;  break;
             case 'X':  $s += 25;  break;
             case 'Y':  $s += 24;  break;
             case 'Z':  $s += 23;  break;
        }
        }

        if( chr($s%26+ord('A'))!=$cf[15] )
        {
            $this->addError($attribute_name, $message);
          }

        //return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     
    public function getLuogoNascita()
    {
        return $this->hasOne(LocComune::className(), ['id' => 'luogo_nascita']);
    }*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComuneResidenza()
    {
        return $this->hasOne(LocComune::className(), ['id' => 'comune_residenza']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUtlOperatori()
    {
        return $this->hasMany(UtlOperatorePc::className(), ['id_anagrafica' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUtlUtenti()
    {
        return $this->hasMany(UtlUtente::className(), ['id_anagrafica' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVolVolontari()
    {
        return $this->hasMany(VolVolontario::className(), ['id_anagrafica' => 'id']);
    }


    public function createOrUpdate()
    {
        $ana = false;
        /*
        if(!$ana && $this->telefono && $this->telefono != ''
            && !empty($this->nome)
            && !empty($this->cognome)
        ) $ana = UtlAnagrafica::find()->where(['telefono'=> $this->telefono, 'nome'=>$this->nome, 'cognome'=>$this->cognome ])->one();

        if(!$ana && $this->email && $this->email != ''
            && !empty($this->nome)
            && !empty($this->cognome)
        ) $ana = UtlAnagrafica::find()->where(['email'=> $this->email, 'nome'=>$this->nome, 'cognome'=>$this->cognome])->one();*/

        if(!$ana && $this->codfiscale && $this->codfiscale != '') $ana = UtlAnagrafica::find()->where(['codfiscale'=> $this->codfiscale])->one();

        if(!$ana && $this->matricola && $this->matricola != '') $ana = UtlAnagrafica::find()->where(['matricola'=> $this->matricola])->one();

        if($ana) :
            // per evitare che svuoti valori presenti in precedenza
            foreach ($this->attributes as $key => $value) :
                if($value && $value != "") : $ana->$key = $value; endif;
            endforeach;
            // aggiorna solo se non viene da MGO
            if(empty($ana->id_sync)) : $ana->save(); endif;
            return $ana;
        endif;

        if(empty($this->id_sync)) : $this->save(); endif;
        return $this;
    }

    /**
     * Pulisci data
     * @param  [type] $insert [description]
     * @return [type]         [description]
     */
    public function beforeSave($insert)
    {
        if($this->data_nascita) :
            $dt = \DateTime::createFromFormat ( 'd-m-Y' , $this->data_nascita );
            if($dt) : $this->data_nascita = Yii::$app->formatter->asDate($dt, 'php:Y-m-d'); endif;
        endif;

        if(!empty($this->codfiscale)) :
            $this->codfiscale = strtoupper($this->codfiscale);
        endif;

        return parent::beforeSave($insert);
    }


    /**
     * Contatti persona
     * @return [type] [description]
     */
    public function getContatto() {
        return $this->hasMany(\common\models\utility\UtlContatto::className(), ['id' => 'id_contatto'])
        ->viaTable('con_anagrafica_contatto', ['id_anagrafica' => 'id']);
    }

    /**
     * Indirizzi persona
     * @return [type] [description]
     */
    public function getIndirizzo() {
        return $this->hasMany(\common\models\utility\UtlIndirizzo::className(), ['id' => 'id_indirizzo'])
        ->viaTable('con_anagrafica_indirizzo', ['id_anagrafica' => 'id']);
    }
}
