<?php
use common\models\ComComunicazioni;
use common\models\LocProvincia;
use common\models\RichiestaMezzoAereo;
use common\models\UtlTipologia;
use common\models\UtlAutomezzoTipo;
use common\models\UtlAttrezzaturaTipo;
use kartik\grid\GridView;
use yii\base\DynamicModel;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use common\models\UtlIngaggio;
use common\models\UtlIngaggioSearchForm;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\export\ExportMenu;
use yii\data\ArrayDataProvider;



function replaceDashAndBar ($txt) {
    return str_replace("||||", ",", str_replace("###", " ", $txt) );
}

$heading = '<h3 class="panel-title"><i class="fas fa-list"></i>  ' .
Html::encode('Lista risorse da cap') . 
'</h3> ';

function getCapMessageDate($v) {
    if(!$v) return '';
    $dt = \DateTime::createFromFormat("Y-m-d H:i:sP", str_replace("T"," ",$v));
    if(is_bool($dt)) return '';

    return $dt->format('d/m/Y H:i:s');
}

$data = [];
$vehicles = [];
foreach ($model->getLastCapMessages()->all() as $cap_message) {
    $json_data = $cap_message->json_content;
    $params = [];
    try{
        $params = $json_data['info']['parameter'];
    } catch(\Exception $e) {

    }
    
    foreach ($params as $param) {
        if($param['valueName'] == 'VEHICLES') {

            $txt = preg_replace_callback('~"[^"]*"~', function ($m) {
                return preg_replace('~\s~', '###', $m[0]);
            }, $param['value']);

            $txt = preg_replace_callback('~"[^"]*"~', function ($m) {
                return preg_replace('~\,~', '||||', $m[0]);
            }, $txt);
            
            $v = explode(" ", $txt);
            foreach ($v as $row) {
                $row_data = explode(",", $row);
                $vehicles[] = [
                    'targa' => isset($row_data[0]) ? replaceDashAndBar( $row_data[0] ) : '',
                    'modello' => isset($row_data[1]) ? replaceDashAndBar( $row_data[1]) : '',
                    'data_1' => isset($row_data[2]) ? getCapMessageDate($row_data[2]) : '',
                    'data_2' => isset($row_data[3]) ? getCapMessageDate($row_data[3]) : '',
                    'data_3' => isset($row_data[4]) ? getCapMessageDate($row_data[4]) : '',
                    'data_4' => isset($row_data[5]) ? getCapMessageDate($row_data[5]) : '',
                    'codice' => isset($row_data[6]) ? replaceDashAndBar( $row_data[6] ) : '',
                ];
            }
        }
    }
}

$cols = [
        [   
            'label' => 'Targa',
            'attribute' => 'targa',
            'width' => '190px',
            'format' => 'raw',
            'value' => function($data) {
                $mezzo_targato = \common\models\UtlAutomezzo::findOne(['targa'=>$data['targa']]);
                if($mezzo_targato) {
                    $odv = \common\models\VolOrganizzazione::findOne($mezzo_targato->idorganizzazione);
                    if($odv) {
                        return '<span title="'.$odv->ref_id.' - '.$odv->denominazione.'" data-toggle="tooltip" data-placement="right">' . $data['targa'] . '</span>';
                    } else {
                        return $data['targa'];
                    }
                    return $data['targa'];
                } else {
                    return $data['targa'];
                }
            }
        ],
        [   
            'label' => 'Modello',
            'attribute' => 'modello'
        ],
        [   
            'label' => 'Partenza',
            'attribute' => 'data_1'
        ],
        [   
            'label' => 'Arrivo',
            'attribute' => 'data_2'
        ],
        [   
            'label' => 'Rientro',
            'attribute' => 'data_3'
        ],
        [   
            'label' => 'Deviato',
            'attribute' => 'data_4'
        ],
        [   
            'label' => 'Codice',
            'attribute' => 'codice'
        ]
    ];

    $dataProvider = new ArrayDataProvider([
            'allModels' => $vehicles,
            'pagination' => false            
        ]);
?>


<?= GridView::widget([
    'id' => 'lista-risorse',
    'dataProvider' => $dataProvider,
    'responsive'=>true,
    'hover'=>true,
    'export' => false,
    'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
    'panel' => [
        'heading'=> ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'initProvider' => true,
        'pjaxContainerId' => 'lista-volontari-attivazioni',
        'clearBuffers' => true,
        'columns' => $cols,
        'target' => ExportMenu::TARGET_BLANK,
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_HTML => false
        ]                
    ]),
    ],
    'pjax'=>false,
    'columns' => $cols,
]); ?>