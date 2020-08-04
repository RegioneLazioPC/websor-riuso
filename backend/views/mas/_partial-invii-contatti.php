<?php

use yii\helpers\Html;
use kartik\grid\GridView;

use common\models\ConMasInvioContact;
use kartik\export\ExportMenu;
?>

<div class="mass-message-template-index">

    <h3>Tentativi di invio ai contatti</h3>


    <?php 

    $cols = [];

    if(empty($searchModel->group)) {
        $cols[] = [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'expandIcon'=>'<span class="fa fa-caret-down"></span>',
                    'collapseIcon'=>'<span style="color: green" class="fa fa-caret-up"></span>',
                    'value' => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'detail'=>function ($model, $key, $index, $column) {
                        return Yii::$app->controller->renderPartial('_partial-invii-expand.php', [
                            'model'=>$model
                        ]);
                    },

                    'detailOptions'=>[
                        'class'=> 'kv-state-enable',
                    ],
                ];


        $cols[] = [

                    'class' => 'kartik\grid\CheckboxColumn',
                    'header' => Html::checkBox('selection_all', false, [
                        'class' => 'select-on-check-all'
                    ]),
                    'checkboxOptions' => function($model, $key, $index, $widget) {
                        return ['value' => $model->id,'class'=>'kv-row-checkbox'];
                    }

                ];

        $cols[] = [
                    'attribute'=>'added',
                    'width'=>'150px',
                    'label' => 'Inviato',
                    'format' => 'raw',
                    'value'=>function($model) use ( $_model ) {
                        return \common\models\MasSingleSend::find()
                        ->where(['id_rubrica_contatto'=>$model->id_rubrica_contatto])
                        ->andWhere(['tipo_rubrica_contatto'=>$model->tipo_rubrica_contatto])
                        ->andWhere(['channel' => $model->channel])
                        ->andWhere(['id_invio'=>$_model->id])
                        ->andWhere(['valore_rubrica_contatto'=>$model->valore_rubrica_contatto])
                        ->andWhere(['status'=>\common\models\MasMessage::STATUS_SEND])
                        ->count() > 0 ? 
                        '<i class="fa fa-check text-success"></i>' : 
                        '<i class="fa fa-close text-danger"></i>';
                    }
                ];


        $cols[] = [
                    'attribute'=>'valore_rubrica_contatto',
                    'label' => 'Contatto',
                ];

        $cols[] = [
                'attribute'=>'channel',
                'width'=>'150px',
                'label' => 'Canale',
                'filter'=> Html::activeDropDownList($searchModel, 'channel', ConMasInvioContact::getCanali(), ['class' => 'form-control','prompt' => 'Tutti']),
                'value'=>function($model) {
                    return $model->channel;
                }
            ];

        


    } else {

        $channels = [];
        if($_model->channel_mail) $channels[] = 'Email';
        if($_model->channel_pec) $channels[] = 'Pec';
        if($_model->channel_fax) $channels[] = 'Fax';
        if($_model->channel_sms) $channels[] = 'Sms';
        if($_model->channel_push) $channels[] = 'Push';


        $cols[] = [
                    'attribute'=>'added',
                    'width'=>'150px',
                    'label' => 'Ricevuto',
                    'format' => 'raw',
                    'value'=>function($model) use ($channels, $_model) {
                        return \common\models\MasSingleSend::find()
                        ->where(['id_rubrica_contatto'=>$model->id_rubrica_contatto])
                        ->andWhere(['tipo_rubrica_contatto'=>$model->tipo_rubrica_contatto])
                        ->andWhere(['id_invio'=>$_model->id])
                        ->andWhere(['status'=>\common\models\MasMessage::STATUS_SEND])
                        ->count() > 0 ? '<i class="fa fa-check text-success" alt="si"></i>' : '<i class="fa fa-close text-danger" alt="no"></i>';
                        
                    }
                ];

        $cols[] = [
                'attribute'=>'added',
                'width'=>'150px',
                'label' => 'Dettagli',
                'format' => 'raw',
                'value'=>function($model) use ($channels, $_model) {
                    $sends = \common\models\MasSingleSend::find()
                    ->where(['id_rubrica_contatto'=>$model->id_rubrica_contatto])
                    ->andWhere(['tipo_rubrica_contatto'=>$model->tipo_rubrica_contatto])
                    ->andWhere(['id_invio'=>$_model->id])
                    ->all();

                    
                    $delivery = [];
                    $delivered = false;

                    foreach ($channels as $channel) {
                        if(!isset($delivery[$channel])) $delivery[$channel] = 0;

                        foreach ($sends as $send) {
                            
                            if($send->channel == $channel) {
                                if($send->status == \common\models\MasMessage::STATUS_SEND) {
                                    $delivery[$channel] = 1;
                                    $delivered = true;
                                }
                            }
                        }

                        
                    }

                    $string = '<div>';

                    foreach ($delivery as $key => $value) {
                        
                        $string .= '' . Html::encode($key) . ': ';
                        $string .= ($value == 1) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-close text-danger"></i>';
                        $string .= '<br />';
                    }

                    $string .= '</div>';
                    
                    return $string;
                }
            ];

    }

    $cols[] = [
                'attribute'=>'valore_riferimento',
                'label' => 'Riferimento',
            ];


    
    $cols[] = [
                'label' => 'Tipo contatto',
                'value' => function ($model) {
                    return (!empty($model->contatto)) ? $model->contatto->tipologia_riferimento : null;
                }
            ];


    ?>



    <?= GridView::widget([
        'id' => 'lista-invii',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
            'before' => $this->render('_search_partial_invii_contatti', [
                'model' => $searchModel, 
                'view' => 'view'
            ]),
        ],
        'columns' => $cols,
    ]); ?>
</div>