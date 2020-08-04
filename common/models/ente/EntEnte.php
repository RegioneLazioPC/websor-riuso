<?php

namespace common\models\ente;

use Yii;

class EntEnte extends \yii\db\ActiveRecord
{
    use \common\traits\Everbridgable;

    const SCENARIO_UPDATE = 'update';

    public $manual_zona_update, $zone_allerta_array;

    /**
     * Necessario a Everbridgable per avere un riferimento all'identificativo in rubrica
     * @return [type] [description]
     */
    protected function getEverbridgeIdentifier() {
        return 'ente_' . $this->id;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ent_ente';
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
        $scenarios[self::SCENARIO_UPDATE] = ['update_zona_allerta_strategy','zone_allerta','zone_allerta_array','manual_zona_update'];
        
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_tipo_ente'], 'integer'],
            [['denominazione', 'id_sync',
                'cf_rappresentante_legale','cf_referente'
            ], 'string'],
            [['codicefiscale', 'partita_iva'], 'string', 'max' => 16 ],
            [['id_tipo_ente'], 'exist', 'skipOnError' => true, 'targetClass' => EntTipoEnte::className(), 'targetAttribute' => ['id_tipo_ente' => 'id']],
            [['update_zona_allerta_strategy'], 'integer'],
            [['zone_allerta'],'string'],
            [['zone_allerta_array'], 'safe'],
            [['manual_zona_update'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_tipo_ente' => 'Tipo Ente',
            'denominazione' => 'Denominazione',
            'codicefiscale' => 'Codicefiscale',
            'partita_iva' => 'Partita Iva'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoEnte()
    {
        return $this->hasOne(EntTipoEnte::className(), ['id' => 'id_tipo_ente']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSedi()
    {
        return $this->hasMany(EntEnteSede::className(), ['id_ente' => 'id']);
    }

    public function getConContatto()
    {
        return $this->hasMany(ConEnteContatto::className(), ['id_ente'=>'id']);
    }

    
    public function getContatto() {
        return $this->hasMany(\common\models\utility\UtlContatto::className(), ['id' => 'id_contatto'])
        ->via('conContatto');
        //->viaTable('con_ente_contatto', ['id_ente' => 'id']);
    }

    public function afterSave( $insert, $changedAttributes )
    {
        parent::afterSave($insert, $changedAttributes);

        if($insert || isset($changedAttributes['update_zona_allerta_strategy']))
        {
            $this->updateZone();
        }
    }
    
    public function updateZone() {
        switch($this->update_zona_allerta_strategy) {
            case 0:
                $cmd = Yii::$app->db->createCommand("UPDATE ent_ente 
                SET 
                zone_allerta = ( SELECT ARRAY_TO_STRING( ARRAY_AGG( distinct z.code ORDER BY z.code ASC), ',') 
                    FROM ent_ente_sede sede
                    LEFT JOIN loc_comune c ON c.id = sede.id_comune
                    LEFT JOIN con_zona_allerta_comune zc ON zc.codistat_comune = c.codistat
                    LEFT JOIN alm_zona_allerta z ON z.id = zc.id_alm_zona_allerta
                    WHERE sede.id_ente = :id_ente )
                WHERE id = :id_ente");
                $cmd->bindValues([
                    ':id_ente' => $this->id
                ]);
            break;
            case 1:
                $cmd = Yii::$app->db->createCommand("UPDATE ent_ente 
                SET 
                zone_allerta = ( SELECT ARRAY_TO_STRING( ARRAY_AGG( distinct z.code ORDER BY z.code ASC), ',') 
                    FROM ent_ente_sede sede
                    LEFT JOIN loc_comune com ON com.id = sede.id_comune
                    LEFT JOIN loc_provincia p ON p.id = com.id_provincia
                    LEFT JOIN loc_comune c ON c.id_provincia = p.id
                    LEFT JOIN con_zona_allerta_comune zc ON zc.codistat_comune = c.codistat
                    LEFT JOIN alm_zona_allerta z ON z.id = zc.id_alm_zona_allerta
                    WHERE sede.id_ente = :id_ente )
                WHERE id = :id_ente");
                $cmd->bindValues([
                    ':id_ente' => $this->id
                ]);
            break;
            case 2:
                $cmd = Yii::$app->db->createCommand("UPDATE ent_ente 
                SET 
                zone_allerta = ( SELECT ARRAY_TO_STRING( ARRAY_AGG( distinct alm_zona_allerta.code ORDER BY alm_zona_allerta.code ASC), ',') FROM alm_zona_allerta )
                WHERE id = :id_ente");
                $cmd->bindValues([
                    ':id_ente' => $this->id
                ]);
            break;
            case 3:
                // void
                return;
            break;
            default:
                return;
            break;
        }

        $cmd->execute();
    }

}
