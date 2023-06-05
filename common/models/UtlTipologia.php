<?php

namespace common\models;

use yii\helpers\Url;
use Yii;

/**
 * This is the model class for table "utl_tipologia".
 *
 * @property integer $id
 * @property integer $idparent
 * @property string $tipologia
 *
 * @property UtlEvento[] $utlEventos
 * @property UtlSegnalazione[] $utlSegnalaziones
 */
class UtlTipologia extends \yii\db\ActiveRecord
{
    public $icon;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'utl_tipologia';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'sammaye\audittrail\LoggableBehavior'
            ]
        ];
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            'icon_url'=> function($model) {
                if(file_exists(Yii::getAlias('@backend/web/images/markers/'.$this->icon_name))){
                    return Yii::$app->urlManagerBackend->createUrl('images/markers/'.$this->icon_name);
                }else{
                    return Yii::$app->urlManagerBackend->createUrl('images/markers/evento-default.png');
                }
            },
            'icon_url_app'=> function($model) {
                if(file_exists(Yii::getAlias('@backend/web/images/markers/'.$this->icon_name))){
                    return Yii::$app->urlManagerApi->createUrl('media/image/markers/'.$this->icon_name.'?icon_date='.$model->icon_date);
                }else{
                    return Yii::$app->urlManagerApi->createUrl('media/image/markers/evento-default.png');
                }
            },
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['icon'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, gif'],
            [['idparent','check_app'], 'integer'],
            [['icon_date'], 'safe'],
            [['tipologia'], 'string', 'max' => 255],
            [['cap_category'],'string'],
            [['valido_dal', 'valido_al'], 'date', 'format'=>'php:Y-m-d']
        ];
    }

    public function upload()
    {
        if ($this->validate()) {


            if($this->icon) {
                $file_path = Yii::getAlias('@backend/web/images');
                if(!file_exists($file_path)) mkdir($file_path);
                if(!file_exists($file_path.'/markers')) mkdir($file_path.'/markers');

                $this->icon->saveAs($file_path.'/markers/' . $this->icon->baseName . '.' . $this->icon->extension);
                $this->icon_name = $this->icon->baseName . '.' . $this->icon->extension;
                $this->icon = null;
                return true;
            } else {
                return true;
            }

        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idparent' => 'Genitore',
            'tipologia' => 'Tipologia',
            'icon_name' => 'Icona'
        ];
    }

    public function getTipologiaGenitore()
    {
        return $this->hasOne(UtlTipologia::className(), ['id' => 'idparent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUtlEventos()
    {
        return $this->hasMany(UtlEvento::className(), ['tipologia_evento' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUtlSegnalaziones()
    {
        return $this->hasMany(UtlSegnalazione::className(), ['tipologia_evento' => 'id']);
    }

    public function getSottostati() {
        return $this->hasMany( EvtSottostatoEvento::className(), ['id' => 'id_sottostato_evento'])
        ->viaTable('con_evt_sottostato_evento_utl_evento', ['id_tipo_evento'=>'id']);
    }
}
