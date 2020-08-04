<?php

namespace common\models\ente;

use Yii;
use common\models\ZonaAllertaStrategy;

class EntTipoEnte extends \yii\db\ActiveRecord
{
    const SCENARIO_UPDATE = 'update';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ent_tipo_ente';
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
            [['descrizione'], 'required'],
            [['descrizione', 'id_sync'], 'string'],
            [['update_zona_allerta_strategy'], 'integer']
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_UPDATE] = ['update_zona_allerta_strategy'];
        
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descrizione' => 'Tipologia',
            'update_zona_allerta_strategy' => 'Strategia di aggiornamento'
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
                        SELECT ent_ente.id as id_ente, ARRAY_TO_STRING(ARRAY_AGG(distinct z.code), ',') as zone FROM ent_ente 
                        LEFT JOIN ent_ente_sede sede ON sede.id_ente = ent_ente.id
                        LEFT JOIN loc_comune c ON c.id = sede.id_comune
                        LEFT JOIN con_zona_allerta_comune zc ON zc.codistat_comune = c.codistat
                        LEFT JOIN alm_zona_allerta z ON z.id = zc.id_alm_zona_allerta
                        WHERE ent_ente.id_tipo_ente = :id_tipo
                        AND ent_ente.update_zona_allerta_strategy <> :zona_manuale
                        GROUP BY ent_ente.id)
                    UPDATE ent_ente SET 
                    update_zona_allerta_strategy = :new_strategy,
                    zone_allerta = (SELECT zone FROM updates WHERE id_ente = ent_ente.id)
                    WHERE 
                    ent_ente.id_tipo_ente = :id_tipo AND 
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
                    "WITH updates as (SELECT ent_ente.id as id_ente, ARRAY_TO_STRING(ARRAY_AGG(distinct z.code ORDER BY z.code ASC), ',') as zone 
                    FROM ent_ente 
                        LEFT JOIN ent_ente_sede sede ON sede.id_ente = ent_ente.id
                        LEFT JOIN loc_comune com ON com.id = sede.id_comune
                        LEFT JOIN loc_provincia p ON p.id = com.id_provincia
                        LEFT JOIN loc_comune c ON c.id_provincia = p.id
                        LEFT JOIN con_zona_allerta_comune zc ON zc.codistat_comune = c.codistat
                        LEFT JOIN alm_zona_allerta z ON z.id = zc.id_alm_zona_allerta
                        WHERE ent_ente.id_tipo_ente = :id_tipo
                        AND ent_ente.update_zona_allerta_strategy <> :zona_manuale
                        GROUP BY ent_ente.id)
                        UPDATE ent_ente SET 
                            update_zona_allerta_strategy = :new_strategy,
                            zone_allerta = (SELECT zone FROM updates WHERE id_ente = ent_ente.id)
                            WHERE 
                            ent_ente.id_tipo_ente = :id_tipo AND 
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
                    UPDATE ent_ente SET 
                    update_zona_allerta_strategy = :new_strategy,
                    zone_allerta = ( SELECT ARRAY_TO_STRING( ARRAY_AGG(alm_zona_allerta.code ORDER BY code ASC), ',') FROM alm_zona_allerta )
                    WHERE 
                    id_tipo_ente = :id_tipo AND
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
                    UPDATE ent_ente SET 
                    update_zona_allerta_strategy = :new_strategy
                    WHERE id_tipo_ente = :id_tipo
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
    public function getEnte()
    {
        return $this->hasMany(EntEnte::className(), ['id_tipo_ente' => 'id']);
    }
}
