<?php

namespace common\models\struttura;

use Yii;

use common\models\ZonaAllertaStrategy;

class StrTipoStruttura extends \yii\db\ActiveRecord
{
    const SCENARIO_UPDATE = 'update';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'str_tipo_struttura';
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
            'update_zona_allerta_strategy' => 'Strategia di aggiornamento zona di allerta'
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
                        SELECT str_struttura.id as id_struttura, ARRAY_TO_STRING(ARRAY_AGG(distinct z.code), ',') as zone FROM str_struttura 
                        LEFT JOIN str_struttura_sede sede ON sede.id_struttura = str_struttura.id
                        LEFT JOIN loc_comune c ON c.id = sede.id_comune
                        LEFT JOIN con_zona_allerta_comune zc ON zc.codistat_comune = c.codistat
                        LEFT JOIN alm_zona_allerta z ON z.id = zc.id_alm_zona_allerta
                        WHERE str_struttura.id_tipo_struttura = :id_tipo
                        AND str_struttura.update_zona_allerta_strategy <> :zona_manuale
                        GROUP BY str_struttura.id)
                    UPDATE str_struttura SET 
                    update_zona_allerta_strategy = :new_strategy,
                    zone_allerta = (SELECT zone FROM updates WHERE id_struttura = str_struttura.id)
                    WHERE 
                    str_struttura.id_tipo_struttura = :id_tipo AND 
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
                    "WITH updates as (SELECT str_struttura.id as id_struttura, ARRAY_TO_STRING(ARRAY_AGG(distinct z.code ORDER BY z.code ASC), ',') as zone 
                    FROM str_struttura 
                        LEFT JOIN str_struttura_sede sede ON sede.id_struttura = str_struttura.id
                        LEFT JOIN loc_comune com ON com.id = sede.id_comune
                        LEFT JOIN loc_provincia p ON p.id = com.id_provincia
                        LEFT JOIN loc_comune c ON c.id_provincia = p.id
                        LEFT JOIN con_zona_allerta_comune zc ON zc.codistat_comune = c.codistat
                        LEFT JOIN alm_zona_allerta z ON z.id = zc.id_alm_zona_allerta
                        WHERE str_struttura.id_tipo_struttura = :id_tipo
                        AND str_struttura.update_zona_allerta_strategy <> :zona_manuale
                        GROUP BY str_struttura.id)
                        UPDATE str_struttura SET 
                            update_zona_allerta_strategy = :new_strategy,
                            zone_allerta = (SELECT zone FROM updates WHERE id_struttura = str_struttura.id)
                            WHERE 
                            str_struttura.id_tipo_struttura = :id_tipo AND 
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
                    UPDATE str_struttura SET 
                    update_zona_allerta_strategy = :new_strategy,
                    zone_allerta = ( SELECT ARRAY_TO_STRING( ARRAY_AGG(alm_zona_allerta.code ORDER BY code ASC), ',') FROM alm_zona_allerta )
                    WHERE 
                    id_tipo_struttura = :id_tipo AND
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
                    UPDATE str_struttura SET 
                    update_zona_allerta_strategy = :new_strategy
                    WHERE id_tipo_struttura = :id_tipo
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
    public function getStruttura()
    {
        return $this->hasMany(StrStruttura::className(), ['id_tipo_struttura' => 'id']);
    }
}
