<?php

use common\components\FilteredActions;
use common\models\ConOperatoreEvento;
use common\models\LocProvincia;
use common\models\UtlEvento;
use common\models\UtlTipologia;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\models\EvtSottostatoEvento;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlEventoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista Veicoli (storico)';
$this->params['breadcrumbs'][] = $this->title;

// Imposta il refresh automatico della grid ogni minuto
$js = "$(document).ready(function() {
            setInterval( function refresh() {
               $.pjax.reload({ container:'#lista-eventi-pjax', timeout:60000 })
            },60000);
      });
";

$this->registerJs($js, $this::POS_READY);

?>

<div class="utl-evento-index">

   

    <?= GridView::widget([
        'id' => 'lista-veicoli-attivi',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive' => true,
        'hover' => true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv' => true, 'xls' => true, 'pdf' => true],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
        ],
        'panel' => [
            'heading' => '<h2 class="panel-title"><i class="glyphicon glyphicon-globe"></i> ' . Html::encode($this->title) . '</h2>',
            'before' => Html::a('<i class="glyphicon glyphicon-repeat"></i> Azzera filtri', ['vehicles-history'], ['class' => 'btn btn-info m10w']),
        ],
        'columns' => [
            [
                'label' => 'Incident',
                'attribute' => 'incident',
                'format' => 'raw',
                'value' => function ($data) {
                    $link = Yii::$app->user->can('viewCapMessage') ? Html::a($data['incident'], ['/cap/single-message', 'id' => $data['id']], ['target'=>'_blank']) : $data['incident'];
                    return  $link;
                }
            ],
            [
                'label' => 'Targa',
                'attribute' => 'targa'
            ],
            [
                'label' => 'Organizzazione',
                'attribute' => 'organizzazione',
                'value' => function ($data) {
                    try {
                        return !empty($data->organizzazione) ? $data->numero_elenco_territoriale . " - " . $data->organizzazione : '';
                    } catch(\Exception $e) {
                        return $data->organizzazione;
                    }
                }
            ],
            [
                'label' => 'Eventi collegati',
                'attribute' => 'null',
                'value' => function ($data) {
                    try {
                        $evts = [];
                        foreach ($data->segnalazioni as $segnalazione) {
                            if (!empty($segnalazione->evento)) {
                                $evts[] = $segnalazione->evento->num_protocollo;
                            }
                        }

                        return implode(", ", $evts);
                    } catch(\Exception $e) {
                        return '';
                    }
                }
            ],
            [
                'label' => 'Data attivazione',
                'attribute' => 'data_attivazione',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'type' => 1,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ],
                'value' => function ($data) {
                    try {
                        return !empty($data->data_attivazione) ? Yii::$app->formatter->asDateTime($data->data_attivazione) : '';
                    } catch(\Exception $e) {
                        return $data->data_attivazione;
                    }
                }
            ],
            [
                'label' => 'Data arrivo',
                'attribute' => 'data_arrivo',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'type' => 1,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ],
                'value' => function ($data) {
                    try {
                        return !empty($data->data_arrivo) ? Yii::$app->formatter->asDateTime($data->data_arrivo) : '';
                    } catch(\Exception $e) {
                        return $data->data_arrivo;
                    }
                }
            ],
            [
                'label' => 'Data chiusura',
                'attribute' => 'data_chiusura',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'type' => 1,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ],
                'value' => function ($data) {
                    try {
                        return !empty($data->data_chiusura) ? Yii::$app->formatter->asDateTime($data->data_chiusura) : '';
                    } catch(\Exception $e) {
                        return $data->data_chiusura;
                    }
                }
            ],
            [
                'label' => 'Data deviazione',
                'attribute' => 'data_deviazione',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'type' => 1,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ],
                'value' => function ($data) {
                    try {
                        return !empty($data->data_deviazione) ? Yii::$app->formatter->asDateTime($data->data_deviazione) : '';
                    } catch(\Exception $e) {
                        return $data->data_deviazione;
                    }
                }
            ]
        ],
    ]); ?>
</div>