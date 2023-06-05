<?php

namespace backend\controllers;

use Exception;
use Yii;
use yii\base\DynamicModel;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\base\Security;
use yii\data\ArrayDataProvider;

class VideowallController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            
        ];
    }

   
    public function actionElicotteri()
    {

        $q = "WITH i as (
            SELECT idautomezzo, idevento FROM utl_ingaggio 
            WHERE 
            DATE_TRUNC('day', created_at) = DATE_TRUNC('day', NOW()) AND stato = 1
            GROUP BY idautomezzo, idevento
        ), voli as (
            SELECT 
            device_id,
            sum( (SELECT v.stop_local_timestamp - v.start_local_timestamp) ) as ore_di_volo,
            max(v.stop_local_timestamp) as stop_local_timestamp,
            max(v.start_local_timestamp) as start_local_timestamp
            FROM utl_arka_voli v
            WHERE v.start_local_timestamp::date = now()::date
            GROUP BY device_id
        ), eli_rich as (
            SELECT 
            *,
            ROW_NUMBER () OVER (
                PARTITION BY id_elicottero
                ORDER BY created_at DESC
            ) as row_num
            FROM richiesta_elicottero er 
            WHERE er.created_at::date = now()::date AND engaged = TRUE
            ORDER BY created_at DESC
        )
        SELECT 
            a.targa as elicottero, 
            re.dataora_decollo as ora_decollo,
            concat ( (DATE_PART('day', now() - re.dataora_decollo) * 24 + 
                       DATE_PART('hour', now() - re.dataora_decollo)) * 60 +
                       DATE_PART('minute', now() - re.dataora_decollo), ' min') as durata_totale,
            e.num_protocollo as protocollo_evento, 
            c.comune as destinazione,
            re.id as scheda_coau,
            voli.ore_di_volo,
            current_volo.ore_di_volo as durata_missione 
        FROM utl_automezzo a
        LEFT JOIN utl_automezzo_tipo t ON t.id = a.idtipo
        LEFT JOIN i ON i.idautomezzo = a.id
        LEFT JOIN utl_evento e ON e.id = i.idevento
        LEFT JOIN loc_comune c ON c.id = e.idcomune 
        LEFT JOIN (
            SELECT eli_rich.* FROM eli_rich
        ) re ON 
                re.idevento = e.id AND 
                re.id_anagrafica_funzionario is not null AND
                re.id_elicottero = a.id AND 
                re.dataora_decollo is not null AND 
                row_num = 1
        LEFT JOIN voli ON voli.device_id = a.device_id
        LEFT JOIN 
            (
                SELECT 
                device_id,
                ROW_NUMBER () OVER (
                    PARTITION BY device_id
                    ORDER BY stop_local_timestamp DESC
                ) as row_num,
                (v.stop_local_timestamp - v.start_local_timestamp) as ore_di_volo
                FROM voli v
                WHERE v.stop_local_timestamp > (NOW() - INTERVAL '20 minutes')
                ORDER BY v.stop_local_timestamp DESC
            ) current_volo ON current_volo.device_id = a.device_id AND current_volo.row_num = 1
        WHERE lower(t.descrizione) = 'elicottero' AND a.device_id is not null
        ORDER BY a.targa ASC";

        $models = Yii::$app->db->createCommand($q)->queryAll();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $models,
            'pagination' => false
        ]);

        return $this->render('elicotteri', [
            'dataProvider' => $dataProvider
        ]);
    }
}
