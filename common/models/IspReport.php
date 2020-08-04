<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "isp_report".
 *
 * @property integer $id
 * @property integer $idispezione
 * @property string $data
 * @property integer $idelemento_esposto
 * @property integer $iddescrizione
 * @property integer $idconclusioni
 * @property string $volume
 * @property string $lunghezza
 * @property string $larghezza
 * @property integer $n_abitanti
 * @property string $descrizione
 * @property string $intervento_brevetermine
 * @property string $intervento_lungotermine
 * @property string $richiami_amministrazione
 *
 * @property IspIspezione $idispezione0
 * @property IspTipoFenomeno $idtipoFenomeno
 */
class IspReport extends \yii\db\ActiveRecord
{
    public $tipo;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'isp_report';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idispezione', 'data'], 'required'],
            [['idispezione'], 'integer'],
            [['tipo'], 'safe'],
            [['volume', 'lunghezza', 'larghezza'], 'number'],
            [['descrizione', 'intervento_brevetermine', 'intervento_lungotermine', 'richiami_amministrazione'], 'string'],
            [['idispezione'], 'exist', 'skipOnError' => true, 'targetClass' => IspIspezione::className(), 'targetAttribute' => ['idispezione' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idispezione' => 'Idispezione',
            'idtipo_fenomeno' => 'Idtipo Fenomeno',
            'idelemento_esposto' => 'Idelemento Esposto',
            'iddescrizione' => 'Iddescrizione',
            'idconclusioni' => 'Idconclusioni',
            'volume' => 'Volume',
            'lunghezza' => 'Lunghezza',
            'larghezza' => 'Larghezza',
            'n_abitanti' => 'N Abitanti',
            'descrizione' => 'Descrizione',
            'intervento_brevetermine' => 'Intervento Brevetermine',
            'intervento_lungotermine' => 'Intervento Lungotermine',
            'richiami_amministrazione' => 'Richiami Amministrazione',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdispezione0()
    {
        return $this->hasOne(IspIspezione::className(), ['id' => 'idispezione']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdtipoFenomeno()
    {
        return $this->hasOne(IspTipoFenomeno::className(), ['id' => 'idtipo_fenomeno']);
    }
}
