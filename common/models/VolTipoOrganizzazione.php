<?php

namespace common\models;

use Yii;

use common\models\ZonaAllertaStrategy;
/**
 * This is the model class for table "vol_tipo_organizzazione".
 *
 * @property integer $id
 * @property string $tipologia
 *
 * @property VolOrganizzazione[] $volOrganizzaziones
 */
class VolTipoOrganizzazione extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vol_tipo_organizzazione';
    }

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
    public function rules()
    {
        return [
            [['tipologia'], 'required'],
            [['tipologia', 'id_sync'], 'string'],
            [['update_zona_allerta_strategy'], 'integer'],
            //[['update_zona_allerta_strategy'],'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipologia' => 'Tipologia',
        ];
    }

    public function afterSave( $insert, $changedAttributes )
    {
        parent::afterSave($insert, $changedAttributes);

        if($insert || isset($changedAttributes['update_zona_allerta_strategy']))
        {
            $this->updateChildrenZoneAllerta();
        }
    }

    public function updateChildrenZoneAllerta() {
        $strategy = $this->update_zona_allerta_strategy;

        switch( $this->update_zona_allerta_strategy ) {
            /**
             * Selezione per comune
             */
            case 0:
                $cmd = Yii::$app->db->createCommand(
                    "WITH updates as (
                        SELECT vol_organizzazione.id as id_organizzazione, ARRAY_TO_STRING(ARRAY_AGG(distinct z.code), ',') as zone FROM vol_organizzazione 
                        LEFT JOIN vol_sede sede ON sede.id_organizzazione = vol_organizzazione.id
                        LEFT JOIN loc_comune c ON c.id = sede.comune
                        LEFT JOIN con_zona_allerta_comune zc ON zc.codistat_comune = c.codistat
                        LEFT JOIN alm_zona_allerta z ON z.id = zc.id_alm_zona_allerta
                        WHERE vol_organizzazione.id_tipo_organizzazione = :id_tipo
                        AND vol_organizzazione.update_zona_allerta_strategy <> :zona_manuale
                        GROUP BY vol_organizzazione.id)
                    UPDATE vol_organizzazione SET 
                    update_zona_allerta_strategy = :new_strategy,
                    zone_allerta = (SELECT zone FROM updates WHERE id_organizzazione = vol_organizzazione.id)
                    WHERE 
                    vol_organizzazione.id_tipo_organizzazione = :id_tipo AND 
                    update_zona_allerta_strategy <> :zona_manuale;");
                $cmd->bindValues([
                    ':zona_manuale' => ZonaAllertaStrategy::getZonaManuale(),
                    ':new_strategy' => $this->update_zona_allerta_strategy,
                    ':id_tipo' => $this->id
                ]);
            break;
            /**
             * Selezione per provincia
             */
            case 1:
                $cmd = Yii::$app->db->createCommand(
                    "WITH updates as (SELECT vol_organizzazione.id as id_organizzazione, ARRAY_TO_STRING(ARRAY_AGG(distinct z.code ORDER BY z.code ASC), ',') as zone 
                    FROM vol_organizzazione 
                        LEFT JOIN vol_sede sede ON sede.id_organizzazione = vol_organizzazione.id
                        LEFT JOIN loc_comune com ON com.id = sede.comune
                        LEFT JOIN loc_provincia p ON p.id = com.id_provincia
                        LEFT JOIN loc_comune c ON c.id_provincia = p.id
                        LEFT JOIN con_zona_allerta_comune zc ON zc.codistat_comune = c.codistat
                        LEFT JOIN alm_zona_allerta z ON z.id = zc.id_alm_zona_allerta
                        WHERE vol_organizzazione.id_tipo_organizzazione = :id_tipo
                        AND vol_organizzazione.update_zona_allerta_strategy <> :zona_manuale
                        GROUP BY vol_organizzazione.id)
                        UPDATE vol_organizzazione SET 
                            update_zona_allerta_strategy = :new_strategy,
                            zone_allerta = (SELECT zone FROM updates WHERE id_organizzazione = vol_organizzazione.id)
                            WHERE 
                            vol_organizzazione.id_tipo_organizzazione = :id_tipo AND 
                            update_zona_allerta_strategy <> :zona_manuale;
                        ");
                $cmd->bindValues([
                    ':zona_manuale' => ZonaAllertaStrategy::getZonaManuale(),
                    ':new_strategy' => $this->update_zona_allerta_strategy,
                    ':id_tipo' => $this->id
                ]);
            break;
            case 2:
                $cmd = Yii::$app->db->createCommand("
                    UPDATE vol_organizzazione SET 
                    update_zona_allerta_strategy = :new_strategy,
                    zone_allerta = ( SELECT ARRAY_TO_STRING( ARRAY_AGG(alm_zona_allerta.code ORDER BY code ASC), ',') FROM alm_zona_allerta )
                    WHERE 
                    id_tipo_organizzazione = :id_tipo AND
                    update_zona_allerta_strategy <> :zona_manuale;");
                $cmd->bindValues([
                    ':zona_manuale' => ZonaAllertaStrategy::getZonaManuale(),
                    ':new_strategy' => $this->update_zona_allerta_strategy,
                    ':id_tipo' => $this->id
                ]);
            break;
            /**
             * Selezione manuale, lascia quelle impostate in precedenza
             */
            case 3:
                $cmd = Yii::$app->db->createCommand("
                    UPDATE vol_organizzazione SET 
                    update_zona_allerta_strategy = :new_strategy
                    WHERE id_tipo_organizzazione = :id_tipo
                    ");
                $cmd->bindValues([
                    ':new_strategy' => $this->update_zona_allerta_strategy,
                    ':id_tipo' => $this->id
                ]);
            break;
        }

        $cmd->execute();
        
    }    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVolOrganizzaziones()
    {
        return $this->hasMany(VolOrganizzazione::className(), ['id_tipo_organizzazione' => 'id']);
    }
}
