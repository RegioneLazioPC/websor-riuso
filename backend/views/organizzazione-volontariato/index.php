<?php

use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\helpers\Html;

use common\models\VolTipoOrganizzazione;
use common\models\VolOrganizzazione;

use common\models\LocProvincia;
use common\models\LocComune;
use common\models\AlmZonaAllerta;
use common\models\TblSezioneSpecialistica;

/* @var $this yii\web\View */
/* @var $searchModel common\models\VolOrganizzazioneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Organizzazioni di volontariato';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-organizzazione-index">

    <p>
        <?php if(Yii::$app->user->can('createOrganizzazione')) echo Html::a('Crea Nuova Organizzazione', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive'=>true,
        'hover'=>true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'panel' => [
            'heading'=>'<h2 class="panel-title"><i class="fa fa-users"></i> '.Html::encode($this->title).'</h2>',
        ],
        'columns' => [
            [
                'label' => 'Num. elenco territoriale',
                'attribute' => 'ref_id'                
            ],
            [
                'label' => 'Tipo',
                'attribute' => 'id_tipo_organizzazione',
                'filter'=> Html::activeDropDownList($searchModel, 'id_tipo_organizzazione', ArrayHelper::map(VolTipoOrganizzazione::find()->asArray()->all(), 'id', 'tipologia'), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data) {
                    return $data['tipoOrganizzazione']['tipologia'];
                }
            ],
            [
                'label' => 'Denominazione',
                'attribute' => 'denominazione',
                'width' => '250px',
                'contentOptions' => ['style'=>'max-width: 250px; white-space: unset;'],
            ],
            [
                'label' => 'Stato',
                'attribute' => 'stato_iscrizione',
                'filter'=> Html::activeDropDownList($searchModel, 'stato_iscrizione', [
                    VolOrganizzazione::STATO_ATTIVA => 'Attiva',
                    -1 => 'Non attiva'
                ], ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data) {
                    return $data->getNomeStato();
                }
            ],
            [
                'label' => 'Comune',
                'attribute' => 'comune',
                'width' => '200px',
                'contentOptions' => ['style'=>'max-width: 200px; white-space: unset;'],
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(
                    LocComune::find()
                    ->where([
                                Yii::$app->params['region_filter_operator'], 
                                'id_regione', 
                                Yii::$app->params['region_filter_id']
                            ])
                    ->orderBy([
                        'comune'=>SORT_ASC, 
                    ])
                    ->all(), 'id', 'comune'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear'=>true,
                    ]
                ],
            ],
            [
                'label' => 'Provincia',
                'attribute' => 'provincia',
                'width' => '130px',
                'contentOptions' => ['style'=>'max-width: 130px; white-space: unset;'],
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(LocProvincia::find()
                    ->where([
                                Yii::$app->params['region_filter_operator'], 
                                'id_regione', 
                                Yii::$app->params['region_filter_id']
                            ])
                    ->all(), 'sigla', 'sigla'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear'=>true,
                    ]
                ],
            ],
            [
                'label' => 'Specializzazioni',
                'attribute' => 'sezione_specialistica',
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(TblSezioneSpecialistica::find()
                    ->all(), 'id', 'descrizione'),
                'width' => '250px',
                'contentOptions' => ['style'=>'max-width: 250px; white-space: unset;'],
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear'=>true,
                    ]
                ],
            ],
            [
                'label' => 'Zone di allerta',
                'attribute' => 'zone_allerta',
                'width' => '250px',
                'contentOptions' => ['style'=>'max-width: 250px; white-space: unset;'],
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(AlmZonaAllerta::find()
                    ->all(), 'code', 'code'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear'=>true,
                    ]
                ],
            ],
            [
                'label' => 'Aggiornamento zone',
                'attribute' => 'update_zona_allerta_strategy',
                'filter'=> Html::activeDropDownList(
                    $searchModel, 'update_zona_allerta_strategy', \common\models\ZonaAllertaStrategy::getStrategies(), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data) {
                    return \common\models\ZonaAllertaStrategy::getStrategyLabel( $data['update_zona_allerta_strategy'] );
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => (Yii::$app->user->can('deleteOrganizzazione')) ? '{view} {update} {delete}' : '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('viewOrganizzazione')){
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio organizzazione'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateOrganizzazione') && empty($model->id_sync)){
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica organizzazione'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    }
                ]
            ],
        ],
    ]); ?>
</div>
