<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "alm_criticita".
 *
 * @property integer $id
 * @property integer $id_allerta
 * @property string $data
 * @property string $ora_inizio
 * @property string $ora_fine
 * @property string $descrizione
 * @property string $tipo
 */
class AlmCriticita extends \yii\db\ActiveRecord
{
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
    public static function tableName()
    {
        return 'alm_criticita';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_allerta'], 'integer'],
            [['data', 'ora_inizio', 'ora_fine'], 'safe'],
            [['descrizione', 'tipo'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_allerta' => 'Id Allerta',
            'data' => 'Data',
            'ora_inizio' => 'Ora Inizio',
            'ora_fine' => 'Ora Fine',
            'descrizione' => 'Descrizione',
            'tipo' => 'Tipo',
        ];
    }

    public function getHours( $start = 0, $end = 86400, $step = 3600, $format = 'H:i' ) {
            $times = array();
            foreach ( range( $start, $end, $step ) as $timestamp ) {
                $hour_mins = gmdate( 'H:i', $timestamp );
                if ( ! empty( $format ) )
                    $times[$hour_mins] = gmdate( $format, $timestamp );
                else $times[$hour_mins] = $hour_mins;
            }
            return $times;
    }

}
