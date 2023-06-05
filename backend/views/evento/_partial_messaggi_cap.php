<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\export\ExportMenu;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;

$heading = '<h3 class="panel-title"><i class="fas fa-list"></i>  ' .
    Html::encode('Lista messaggi cap') .
    '</h3> ';

$capMessages = $model->capMessagesFromReference;

function getCapMessDate($v)
{
    if (!$v) return '';
    $dt = \DateTime::createFromFormat("Y-m-d H:i:sP", str_replace("T", " ", $v));
    if (is_bool($dt)) return '';

    return $dt->format('d/m/Y H:i:s');
}

// $data = [];
// foreach ($model->getLastCapMessages()->all() as $cap_message) {
//     $json_data = $cap_message->json_content;


//     $params = [];
//     try {
//         $params = $json_data['info']['parameter'];
//     } catch (\Exception $e) {
//     }
//     $vehicles = [];
//     foreach ($params as $param) {
//         if ($param['valueName'] == 'VEHICLES') {

//             $v = explode(" ", $param['value']);
//             foreach ($v as $row) {
//                 $row_data = explode(",", $row);
//                 $vehicles[] = [
//                     'targa' => isset($row_data[0]) ? $row_data[0] : '',
//                     'modello' => isset($row_data[1]) ? $row_data[1] : '',
//                     'data_1' => isset($row_data[2]) ? getCapMessageDate($row_data[2]) : '',
//                     'data_2' => isset($row_data[3]) ? getCapMessageDate($row_data[3]) : '',
//                     'data_3' => isset($row_data[4]) ? getCapMessageDate($row_data[4]) : '',
//                     'data_4' => isset($row_data[5]) ? getCapMessageDate($row_data[5]) : '',
//                     'codice' => isset($row_data[6]) ? $row_data[6] : '',
//                 ];
//             }
//         }
//     }
// }

$cols = [
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{view}',
        'buttons' => [
            'view' => function ( $url, $model ) {
                if(Yii::$app->user->can('viewCapMessage')){
                    $url = ['/cap/single-message', 'id' => $model->id];
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>&nbsp;&nbsp;', $url, [
                        'title' => Yii::t('app', 'Vedi'),
                        'data-toggle'=>'tooltip'
                    ]) ;
                }else{
                    return '';
                }
            },
        ],
    ],
    [
        'label' => 'Codice',
        'attribute' => 'identifier',
        'width' => '190px'
    ],
    [
        'label' => 'Gestito da',
        'attribute' => 'sender_name'
    ],
    [
        'label' => 'Evento',
        'attribute' => 'event_type'
    ],
    [
        'label' => 'Tipo',
        'attribute' => 'event_subtype'
    ],
    [
        'label' => 'Data e ora',
        'attribute' => 'date_creation'
    ],
    [
        'label' => 'Comune',
        'attribute' => 'string_comune'
    ],
    [
        'label' => 'Indirizzo',
        'attribute' => 'codice',
        'format' => 'raw',
        'value' => function ($model) {
            $linkMap = Html::a('Map', Url::to($model->json_content['info']['web'], true), ['target' => '_blank']);
            return $model->json_content['info']['area']['areaDesc'] . " - " . $linkMap;
        }
    ]
];

$dataProvider = new ArrayDataProvider([
    'allModels' => $capMessages,
    'pagination' => false
]);
?>


<?= GridView::widget([
    'id' => 'lista-cap-messages',
    'dataProvider' => $dataProvider,
    'responsive' => true,
    'hover' => true,
    'export' => false,
    'exportConfig' => ['csv' => true, 'xls' => true, 'pdf' => true],
    'panel' => [
        'heading' => ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'initProvider' => true,
            'pjaxContainerId' => 'lista-cap-messages',
            'clearBuffers' => true,
            'columns' => $cols,
            'target' => ExportMenu::TARGET_BLANK,
            'exportConfig' => [
                ExportMenu::FORMAT_TEXT => false,
                ExportMenu::FORMAT_HTML => false
            ]
        ]),
    ],
    'pjax' => false,
    'columns' => $cols,
]); ?>