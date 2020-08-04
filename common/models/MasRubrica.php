<?php

namespace common\models;

use Yii;
use common\models\utility\UtlIndirizzo;
use common\models\utility\UtlContatto;
use common\models\UtlAnagrafica;
use nanson\postgis\behaviors\GeometryBehavior;

/**
 * This is the model class for table "mas_rubrica".
 *
 * @property int $id
 * @property string $dettagli
 * @property string $ruolo
 * @property int $id_anagrafica
 * @property int $id_indirizzo
 * @property double $lat
 * @property double $lon
 * @property int $created_at
 * @property int $updated_at
 * @property string $geom
 *
 * @property UtlAnagrafica $anagrafica
 * @property UtlIndirizzo $indirizzo
 */
class MasRubrica extends \yii\db\ActiveRecord
{
    use \common\traits\Everbridgable;
    /**
     * Necessario a Everbridgable per avere un riferimento all'identificativo in rubrica
     * @return [type] [description]
     */
    protected function getEverbridgeIdentifier() {
        return 'mas_rubrica_' . $this->id;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mas_rubrica';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            yii\behaviors\TimestampBehavior::className(),
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ],
            [
                'class' => GeometryBehavior::className(),
                'type' => GeometryBehavior::GEOMETRY_POINT,
                'attribute' => 'geom',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dettagli', 'geom'], 'string'],
            [['id_anagrafica', 'id_indirizzo', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['id_anagrafica', 'id_indirizzo', 'created_at', 'updated_at'], 'integer'],
            [['lat', 'lon'], 'number'],
            [['ruolo'], 'string', 'max' => 255],
            [['ruolo'],'required'],
            [['id_anagrafica'], 'exist', 'skipOnError' => true, 'targetClass' => UtlAnagrafica::className(), 'targetAttribute' => ['id_anagrafica' => 'id']],
            [['id_indirizzo'], 'exist', 'skipOnError' => true, 'targetClass' => UtlIndirizzo::className(), 'targetAttribute' => ['id_indirizzo' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dettagli' => 'Dettagli',
            'ruolo' => 'Ruolo',
            'id_anagrafica' => 'Anagrafica',
            'id_indirizzo' => 'Indirizzo',
            'lat' => 'Latitudine',
            'lon' => 'Longitudine',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'geom' => 'Geom',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnagrafica()
    {
        return $this->hasOne(UtlAnagrafica::className(), ['id' => 'id_anagrafica']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIndirizzo()
    {
        return $this->hasOne(UtlIndirizzo::className(), ['id' => 'id_indirizzo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContatto()
    {
        return $this->hasMany(UtlContatto::className(), ['id' => 'id_contatto'])
        ->viaTable('con_mas_rubrica_contatto', ['id_mas_rubrica'=>'id']);
    }

    /**
     * Inserisci geom field
     * @param  [type] $insert [description]
     * @return [type]         [description]
     */
    public function beforeSave($insert) {
        
        if($this->lat && $this->lon) $this->geom = [$this->lon, $this->lat];
        return parent::beforeSave($insert);
    }
}
