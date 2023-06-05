<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;


$query = $_model->getInvio()->orderBy( ['id' => SORT_DESC] );
$dataProvider = new ActiveDataProvider([
    'query' => $query
]);

?>

<div class="mass-message-template-index">

    <?= GridView::widget([
        'id' => 'lista-invii-messaggio',
        'dataProvider' => $dataProvider,
        'responsive'=>true,
        'hover'=>true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'toggleData'=>false,
        'pjax'=>true,
        'pjaxSettings'=>[
            'neverTimeout'=> true,
        ],
        'panel' => [
            'heading'=> "Elenco tentativi di invio",
        ],
        'columns' => [
            [
                'attribute'=>'id',
                'label' => 'Identificativo',
                'value'=>function($model) {
                    return "#".$model->id;
                }
            ],
            [
                'attribute' =>'created_at',
                'label'=>'Data',
                'format'=>'datetime'
            ],
            [
                'label' => 'Creato da',
                'attribute' => 'id_user',
                'value' => function($model){
                    return @$model->user->username;
                }
            ],
            [
                'header' => 'Destinatari',
                'attribute' => 'id',
                'width' => '50px',
                'value' => function($data){
                    return \common\models\MasSingleSend::find()
                    ->from(['t' => '(SELECT distinct on (id_rubrica_contatto, tipo_rubrica_contatto, id_invio) * FROM con_mas_invio_contact)'])->where(['id_invio'=>$data->id])->count();
                }
            ],
            [
                'header' => '<i class="fa fa-check text-success"></i>',
                'attribute' => 'id',
                'width' => '50px',
                'value' => function($data){

                    if(!empty($data->mas_ref_id)) {        
                
                        $result = Yii::$app->db->createCommand("SELECT count( distinct (id_rubrica_contatto, tipo_rubrica_contatto) ) as num_recapitati FROM con_mas_invio_contact WHERE valore_rubrica_contatto in (
                            SELECT distinct recapito as rec
                            FROM mas_v2_feedback m
                            WHERE m.id_invio = :id_invio
                            AND ( (\"status\" in (2,3) AND m.channel not in ('Pec','Fax','Sms')) OR (\"status\" = 3 and m.channel in ('Pec','Fax','Sms')))
                            )
                            AND id_invio = :id_invio", [':id_invio' => $data->id]
                        )
                        ->queryAll();
                        $delivered = isset($result[0]) ? $result[0]['num_recapitati'] : 0;
                        
                        
                    } else {


                        $connection = Yii::$app->getDb();
                        $command = $connection->createCommand("WITH t as (SELECT count(\"status\") FILTER (
                                WHERE 
                                    (\"status\" in (:sent,:received) AND channel not in ('Pec','Fax','Sms')) OR 
                                    (\"status\" = :received and channel in ('Pec','Fax','Sms'))
                                    ) > 0 as delivered 
                            FROM mas_single_send m WHERE id_invio = :id_invio
                            GROUP BY tipo_rubrica_contatto, id_rubrica_contatto)
                            SELECT count(delivered) FILTER (WHERE delivered is true) as delivered FROM t;
                                ;", [
                            ':id_invio' => intval($data->id),
                            ':sent' => \common\models\MasMessage::STATUS_SEND[0],
                            ':received' => \common\models\MasMessage::STATUS_RECEIVED[0]
                        ]);

                        $result = $command->queryAll();
                        $delivered = $result[0]['delivered'];

                    }

                    
                    return $delivered;/*
                    return \common\models\MasSingleSend::find()
                     ->from(['t' => '(SELECT distinct on (id_rubrica_contatto, tipo_rubrica_contatto, id_invio) * FROM mas_single_send)'])
                    ->where(['id_invio'=>$data->id])
                    ->andWhere(
                        ['or',
                            ['and',
                                ['status'=>\common\models\MasMessage::STATUS_SEND],
                                ['not in', 'channel', ['Pec','Fax']]
                            ],
                            ['and',
                                ['status'=>\common\models\MasMessage::STATUS_RECEIVED],
                                ['in', 'channel', ['Pec','Fax']]
                            ]
                        ]
                    )->count();*/

                }
            ],
            [
                'header' => '<i class="fa fa-close text-danger"></i>',
                'attribute' => 'id',
                'width' => '50px',
                'value' => function($data){


                    $total = \common\models\MasSingleSend::find()
                    ->from(['t' => '(SELECT distinct on (id_rubrica_contatto, tipo_rubrica_contatto, id_invio) * FROM con_mas_invio_contact)'])->where(['id_invio'=>$data->id])->count();

                    if(!empty($data->mas_ref_id)) {        
                
                        $result = Yii::$app->db->createCommand("SELECT count( distinct (id_rubrica_contatto, tipo_rubrica_contatto) ) as num_recapitati FROM con_mas_invio_contact WHERE valore_rubrica_contatto in (
                            SELECT distinct recapito as rec
                            FROM mas_v2_feedback m
                            WHERE m.id_invio = :id_invio
                            AND ( (\"status\" in (2,3) AND m.channel not in ('Pec','Fax','Sms')) OR (\"status\" = 3 and m.channel in ('Pec','Fax','Sms')))
                            )
                            AND id_invio = :id_invio", [':id_invio' => $data->id]
                        )
                        ->queryAll();
                        $delivered = isset($result[0]) ? $result[0]['num_recapitati'] : 0;
                        
                        
                    } else {


                        $connection = Yii::$app->getDb();
                        $command = $connection->createCommand("WITH t as (SELECT count(\"status\") FILTER (
                                WHERE 
                                    (\"status\" in (:sent,:received) AND channel not in ('Pec','Fax','Sms')) OR 
                                    (\"status\" = :received and channel in ('Pec','Fax','Sms'))
                                    ) > 0 as delivered 
                            FROM mas_single_send m WHERE id_invio = :id_invio
                            GROUP BY tipo_rubrica_contatto, id_rubrica_contatto)
                            SELECT count(delivered) FILTER (WHERE delivered is true) as delivered FROM t;
                                ;", [
                            ':id_invio' => intval($data->id),
                            ':sent' => \common\models\MasMessage::STATUS_SEND[0],
                            ':received' => \common\models\MasMessage::STATUS_RECEIVED[0]
                        ]);

                        $result = $command->queryAll();
                        $delivered = $result[0]['delivered'];

                    }


                    
                    return $total - $delivered;
                    /*
                    $subquery = "(SELECT count(id) FROM 
                        mas_single_send WHERE
                        id_rubrica_contatto = t.id_rubrica_contatto AND
                        tipo_rubrica_contatto = t.tipo_rubrica_contatto AND 
                        id_invio = t.id_invio AND 
                        status in (2,3)
                    )";
                    return \common\models\MasSingleSend::find()
                        ->from(['t' => '(SELECT distinct on (id_rubrica_contatto, tipo_rubrica_contatto, id_invio) * FROM mas_single_send)'])
                        ->where(['id_invio'=>$data->id])
                        ->andWhere([
                            "=",$subquery,0
                        ])->count();*/

                }
            ],
            [
                'label' => 'Email',
                'attribute' => 'channel_mail',
                'format'=>'raw',
                'width' => '50px',
                
                'value' => function($data){
                    return ($data->channel_mail == 1) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-close text-danger"></i>';
                }
            ],
            [
                'label' => 'Pec',
                'attribute' => 'channel_pec',
                'width' => '50px',
                'format'=>'raw',
                'value' => function($data){
                    return ($data->channel_pec == 1) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-close text-danger"></i>';
                }
            ],
            [
                'label' => 'Fax',
                'attribute' => 'channel_fax',
                'width' => '50px',
                'format'=>'raw',
                'value' => function($data){
                    return ($data->channel_fax == 1) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-close text-danger"></i>';
                }
            ],
            [
                'label' => 'Sms',
                'attribute' => 'channel_sms',
                'width' => '50px',
                'format'=>'raw',
                'value' => function($data){
                    return ($data->channel_sms == 1) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-close text-danger"></i>';
                }
            ],
            [
                'label' => 'Push',
                'attribute' => 'channel_push',
                'width' => '50px',
                'format'=>'raw',
                'value' => function($data){
                    return ($data->channel_push == 1) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-close text-danger"></i>';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('sendMasMessage')){
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>&nbsp;&nbsp;', ['view-invio', 'id_invio'=>$model->id], [
                                'title' => Yii::t('app', 'Dettagli'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    }
                ],
            ]
        ],
    ]); ?>
</div>