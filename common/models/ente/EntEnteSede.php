<?php

namespace common\models\ente;

use Yii;
use nanson\postgis\behaviors\GeometryBehavior;

use common\models\LocComune;

class EntEnteSede extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ent_ente_sede';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_ente', 'id_comune', 'indirizzo', 'tipo'], 'required'],
            [['id_ente', 'id_comune'], 'default', 'value' => null],
            [['id_ente', 'id_comune', 'tipo'], 'integer'],
            [['indirizzo', 'cap'], 'string'],
            [['lat', 'lon', 'coord_x', 'coord_y'], 'number'],
            [['id_sync'], 'string', 'max' => 255],
            [['id_ente'], 'exist', 'skipOnError' => true, 'targetClass' => EntEnte::className(), 'targetAttribute' => ['id_ente' => 'id']]
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior',
                'ignored' => ['geom']
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
            'id_ente' => 'Organizzazione',
            'indirizzo' => 'Indirizzo',
            'id_comune' => 'Comune',
            'tipo' => 'Tipo',
            'lat' => 'Lat',
            'lon' => 'Lon',
            'coord_x' => 'Coordinate Rm40 x',
            'coord_y' => 'Coordinate Rm40 y',
            'cap' => 'Cap'
        ];
    }

   

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocComune()
    {
        return $this->hasOne(LocComune::className(), ['id' => 'id_comune']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnte()
    {
        return $this->hasOne(EntEnte::className(), ['id' => 'id_ente']);
    }

    /**
     * 
     * @param  [type] $insert [description]
     * @return [type]         [description]
     */
    public function beforeSave($insert) {
        
        
        return parent::beforeSave($insert);
    }

    public function afterSave( $insert, $changedAttributes )
    {
        parent::afterSave($insert, $changedAttributes);

        $this->ente->updateZone();
        
    }

    public function getConContatto()
    {
        return $this->hasMany(ConEnteSedeContatto::className(), ['id_ente_sede'=>'id']);
    }

    /**
     * Contatti della sede
     * @return [type] [description]
     */
    public function getContatto() {
        return $this->hasMany(\common\models\utility\UtlContatto::className(), ['id' => 'id_contatto'])
        ->via('conContatto');
    }
}
