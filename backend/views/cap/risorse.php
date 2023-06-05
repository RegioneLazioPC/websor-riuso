<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;


/* @var $this yii\web\View */
/* @var $searchModel common\models\RubricaGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use common\models\cap\CapResources;

$dial = [];
    foreach (CapResources::$avaible_profiles as $record) {
        $dial[$record] = $record;
    }
$feeds = [];
    foreach (CapResources::$selectable_feeds as $record) {
        $feeds[$record] = $record;
    }
$auths = [];
    foreach (CapResources::$avaible_autentications as $record) {
        $auths[$record] = $record;
    }


$this->title = 'Risorse CAP';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="risorse-cap-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if(Yii::$app->user->can('createCapResource')) echo Html::a('Crea nuova risorsa', ['create-risorsa'], ['class' => 'btn btn-success']); ?>
    </p>

    <?php 

    $cols = [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {release} {delete} {ripristina} {lockunlock}',
                'buttons' => [
                    'ripristina' => function ($url, $model) {
                        if(Yii::$app->user->can('createCapResource') && $model->removed == 1){
                            $url = ['/cap/ripristina-risorsa', 'id' => $model->id];
                            return Html::a('<span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Ripristina'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'lockunlock' => function ($url, $model) {
                        if(Yii::$app->user->can('updateCapResource')){
                            $url = ['/cap/lock-unlock-risorsa', 'id' => $model->id];
                            return ($model->locked == 1) ? Html::a('<span class="glyphicon glyphicon-upload"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Riattiva elaborazione'),
                                'data-toggle'=>'tooltip'
                            ]) : Html::a('<span class="glyphicon glyphicon-download"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Blocca elaborazione'),
                                'data-toggle'=>'tooltip'
                            ]);
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateCapResource')){
                            $url = ['/cap/update-risorsa', 'id' => $model->id];
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Aggiorna'),
                                'data-toggle'=>'tooltip'
                            ]);
                        }else{
                            return '';
                        }
                    },
                    'release' => function ($url, $model) {
                        if(Yii::$app->user->can('updateCapResource')){
                            $url = ['/cap/rilascia-semaforo', 'id' => $model->id];
                            return Html::a('<span class="glyphicon glyphicon-minus-sign"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Rilascia semaforo'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'delete' => function ($url, $model) {
                        if(Yii::$app->user->can('deleteCapResource')){
                            $url = ['/cap/delete-risorsa', 'id' => $model->id];
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Elimina'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    }
                ],
            ],
            [
                'attribute' => 'last_check',
                'label' => 'Ultima elaborazione',
                'value' => function($data) {
                    $date = \DateTime::createFromFormat('U', $data->last_check);
                    if(is_bool($date)) return null;

                    $date->setTimezone( (new \DateTimeZone('Europe/Rome')) );
                    return $date->format('d/m/Y H:i:s');
                }
            ],
            'id',
            'identifier',
            [
                'attribute'=>'expiry',
                'label'=>'Ore scadenza'
            ],
            [
                'label' => 'Elaborazione bloccata',
                'attribute' => 'locked',
                'value' => function($model) {
                    return $model->locked == 1 ? 'Si' : 'No';
                }
            ],
            [
                'label' => 'Rimossa',
                'attribute' => 'removed',
                'value' => function($model) {
                    return $model->removed == 1 ? 'Si' : 'No';
                }
            ],
            [
                'label' => 'URL RSS',
                'attribute' => 'url_feed_rss',
                'width' => '250px',
                'contentOptions' => ['style'=>'width: 250px; max-width: 250px; white-space: initial; word-wrap: break-word;'],
            ],
            [
                'label' => 'URL ATOM',
                'attribute' => 'url_feed_atom',
                'width' => '250px',
                'contentOptions' => ['style'=>'width: 250px; max-width: 250px;white-space: initial; word-wrap: break-word;'],
            ],
            [
                'attribute' => 'preferred_feed',
                'filter'=> Html::activeDropDownList($searchModel, 'preferred_feed', $feeds, ['class' => 'form-control','prompt' => 'Tutti']),
            ],
            [
                'attribute' => 'profile',
                'filter'=> Html::activeDropDownList($searchModel, 'profile', $dial, ['class' => 'form-control','prompt' => 'Tutti']),
            ],
            'raggruppamento',
            [
                'attribute' => 'autenticazione',
                'filter'=> Html::activeDropDownList($searchModel, 'autenticazione', $auths, ['class' => 'form-control','prompt' => 'Tutti']),
            ],
            [
                'label' => 'USERNAME',
                'attribute' => 'username',
                'width' => '250px',
                'contentOptions' => ['style'=>'width: 250px; max-width: 250px;white-space: initial; word-wrap: break-word;'],
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
            'heading'=> "Scarica risorse CAP " . ExportMenu::widget([
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
