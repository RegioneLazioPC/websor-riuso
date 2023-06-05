<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $searchModel common\models\RubricaGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use common\models\cap\CapConsumer;


$list_comuni   = \common\models\LocComuneGeom::find()->select('pro_com,comune')
    ->where(['cod_reg' => Yii::$app->params['region_filter_id']])
    ->orderBy(['comune'=>SORT_ASC])
    ->all();
$comuni = ArrayHelper::map( $list_comuni,'pro_com','comune');



$this->title = 'Consumer CAP';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="risorse-cap-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if(Yii::$app->user->can('createCapConsumer')) echo Html::a('Crea nuovo consumer', ['create-consumer'], ['class' => 'btn btn-success']); ?>
    </p>

    <?php 

    $cols = [
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {password} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            if(Yii::$app->user->can('updateCapConsumer')){
                                $url = ['/cap/update-consumer', 'id' => $model->id];
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;', $url, [
                                    'title' => Yii::t('app', 'Aggiorna'),
                                    'data-toggle'=>'tooltip'
                                ]);
                            }else{
                                return '';
                            }
                        },
                        'password' => function ($url, $model) {
                            if(Yii::$app->user->can('updateCapConsumerPassword')){
                                $url = ['/cap/update-consumer-password', 'id' => $model->id];
                                return Html::a('<span class="glyphicon glyphicon-lock"></span>&nbsp;&nbsp;', $url, [
                                    'title' => Yii::t('app', 'Aggiorna password'),
                                    'data-toggle'=>'tooltip'
                                ]);
                            }else{
                                return '';
                            }
                        },
                        'delete' => function ($url, $model) {
                            if(Yii::$app->user->can('deleteCapConsumer')){
                                $url = ['/cap/delete-consumer', 'id' => $model->id];
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;', $url, [
                                    'title' => Yii::t('app', 'Elimina'),
                                    'data-toggle'=>'tooltip'
                                ]) ;
                            }else{
                                return '';
                            }
                        }
                    ]
                ],
                'id',
                [
                    'label' => 'Username',
                    'attribute' => 'username'
                ],
                [
                    'label' => 'Address',
                    'attribute' => 'address'
                ],
                [
                    'label' => 'Abilitato',
                    'attribute' => 'enabled',
                    'value' => function($model) {
                        return $model->enabled == 1 ? 'Si' : 'No';
                    }
                ],
                [
                    'label' => 'Sala operativa',
                    'attribute' => 'sala_operativa',
                    'value' => function($model) {
                        return $model->sala_operativa == 1 ? 'Si' : 'No';
                    }
                ],
                [
                    'label' => 'Comuni',
                    'attribute' => 'comuni',
                    'value' => function($model) use ($comuni) {

                        if(!empty($model->comuni)) {
                            $c_array = json_decode($model->comuni);
                            $cm = [];
                            foreach ($c_array as $v) {
                                $cm[] = $comuni[$v];
                            }
                            return implode(", ", $cm);
                        } else {
                            return '';
                        }

                    }
                ],
            ];

    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterModel' => $searchModel,
        'responsive'=>true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'hover'=>true,
        'pjax'=>true,
        'toggleData'=>false,
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'pjaxSettings'=>[
            'neverTimeout'=> true,
        ],
        'panel' => [
            'heading'=> "Scarica lista consumer CAP " . ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $cols,
                'target' => ExportMenu::TARGET_BLANK,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false
                ]                
            ])
        ],
        'pager' => [
            'firstPageLabel' => 'Pagina iniziale',
            'lastPageLabel'  => 'Pagina finale'
        ],
        'columns' => $cols,
    ]); ?>
</div>
