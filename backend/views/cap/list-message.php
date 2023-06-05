<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;

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


$this->title = 'Risorse CAP ' . Yii::$app->request->get('raggruppamento');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="risorse-cap-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?php $cols = [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {createsegnalazione}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if (Yii::$app->user->can('viewCapMessage')) {
                            try {
                                $url = ['/cap/single-message', 'id' => $model->id];
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>&nbsp;&nbsp;', $url, [
                                    'title' => Yii::t('app', 'Vedi'),
                                    'data-toggle'=>'tooltip'
                                ]) ;
                            } catch (\Exception $e) {
                                return '';
                                Yii::error($e->getMessage());
                            }
                        } else {
                            return '';
                        }
                    },
                    'createsegnalazione' => function ($url, $model) {
                        if (Yii::$app->user->can('createSegnalazione')) {
                            try {
                                $url = ['/segnalazione/create', 'from_cap' => $model->id];
                                return Html::a('<span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;', $url, [
                                    'title' => Yii::t('app', 'Crea segnalazione'),
                                    'data-toggle'=>'tooltip'
                                ]) ;
                            } catch (\Exception $e) {
                                return '';
                                Yii::error($e->getMessage());
                            }
                        } else {
                            return '';
                        }
                    }
                ],
            ],
            //'id',
            [
                'attribute' => 'sent_rome_timezone',
                'format' => 'raw',
                'value' => function ($data) {
                    $title = '';
                    
                    $call_time = \DateTime::createFromFormat('Y-m-d H:i:sP', $data->call_time);
                    if (!is_bool($call_time)) {
                        $title .= "Chiamata: " . $call_time->format('d/m/Y H:i:s') . "\n";
                    }
                    $intervent_time = \DateTime::createFromFormat('Y-m-d H:i:sP', $data->intervent_time);
                    if (!is_bool($intervent_time)) {
                        $title .= "Intervento: " . $intervent_time->format('d/m/Y H:i:s') . "\n";
                    }
                    $arrival_time = \DateTime::createFromFormat('Y-m-d H:i:sP', $data->arrival_time);
                    if (!is_bool($arrival_time)) {
                        $title .= "Arrivo: " . $arrival_time->format('d/m/Y H:i:s') . "\n";
                    }
                    $close_time = \DateTime::createFromFormat('Y-m-d H:i:sP', $data->close_time);
                    if (!is_bool($close_time)) {
                        $title .= "Chiusura: " . $close_time->format('d/m/Y H:i:s') . "\n";
                    }
                    $expiry_time = \DateTime::createFromFormat('Y-m-d H:i:s', $data->expires_rome_timezone);
                    if (!is_bool($expiry_time)) {
                        $title .= "Scadenza: " . $expiry_time->format('d/m/Y H:i:s') . "\n";
                    }

                    $string = '<span class="fas fa-info m5w" title="'.$title.'" data-toggle="tooltip"></span>';

                    $d = \DateTime::createFromFormat('Y-m-d H:i:sP', $data->sent);
                    if (is_bool($d)) {
                        return $string;
                    }

                    $d->setTimezone((new \DateTimeZone('Europe/Rome')));
                    return $d->format('d/m/Y H:i:s') . " " . $string;
                }
            ],
            [
                'attribute' => 'string_comune',
                'width' => '200px',
                'contentOptions' => [],
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => array_merge([null=>'Tutti'], ArrayHelper::map(
                    \common\models\LocComune::find()
                    ->where([
                            Yii::$app->params['region_filter_operator'],
                            'id_regione',
                            Yii::$app->params['region_filter_id']
                        ])
                    ->orderBy([
                    'comune'=>SORT_ASC,
                    ])
                    ->all(),
                    'comune',
                    'comune'
                )),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ]
                ],
            ],
            [
                'attribute' => 'string_provincia',
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => array_merge([null=>'Tutti'], ArrayHelper::map(
                    \common\models\LocProvincia::find()
                    ->where([
                            Yii::$app->params['region_filter_operator'],
                            'id_regione',
                            Yii::$app->params['region_filter_id']
                        ])
                    ->orderBy([
                    'sigla'=>SORT_ASC,
                    ])
                    ->all(),
                    'sigla',
                    'sigla'
                )),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'allowClear'=>true,
                    ]
                ],
            ],
            [
                'attribute' => 'via',
                'width' => '130px',
                'contentOptions' => [],
                'format' => 'raw',
                'value' => function ($data) {
                    try {
                        return is_string($data->json_content['info']['area']['areaDesc'])
                        ? $data->json_content['info']['area']['areaDesc']
                        : '';
                    } catch (\Exception $e) {
                        return $e->getMessage();
                    }
                }
            ],
            'event_type',
            'event_subtype',
            [
                'attribute' => 'eventi_websor',
                'label' => 'Eventi websor',
                'format' => 'raw',
                'value' => function ($data) {

                    if (!is_string($data->incident)) {
                        return '';
                    }


                    $q = Yii::$app->db->createCommand(
                        "SELECT distinct e.num_protocollo, e.id FROM con_evento_segnalazione ce 
                        LEFT JOIN utl_evento e ON e.id = ce.idevento
                        LEFT JOIN utl_segnalazione s ON s.id = ce.idsegnalazione
                        WHERE s.id_cap_message IN (SELECT id FROM view_cap_messages WHERE incident = :incident);",
                        [':incident'=>$data->incident]
                    );
                    $res = $q->queryAll();

                    $ids = array_map(function ($row) {
                        try {
                            $i = null;
                            $protocollo = explode("/", $row['num_protocollo']);
                            /*if(is_array($row['id'])) {
                                $i = $row['id'][0];
                            } else {
                                $i = $row['id'];
                            }*/
                            $protocollo = $protocollo[0];

                            return Html::a($row['num_protocollo'], [
                                '/evento/view',
                                'id' => $protocollo
                            ], [
                                'class' => '',
                                'target' => '_blank'
                            ]);
                        } catch (\Exception $e) {
                            Yii::error($e->getMessage());
                            return null;
                        }
                    }, $res);

                    if (!is_string(implode(", ", $ids))) {
                        return "-";
                    } else {
                        return implode(", ", $ids);
                    }
                }
            ],
            'formatted_status',
            'code_int',
            'code_call',
            'type',
            'status',
            
            [
                'attribute' => 'major_event',
                'filter'=> Html::activeDropDownList($searchModel, 'profile', [0=>'No', 1=>'Si'], ['class' => 'form-control','prompt' => 'Tutti']),
            ],
        ];
?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
            'heading'=> "Scarica messaggi CAP " . ExportMenu::widget([
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
