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


$heading = '<h3 class="panel-title"><i class="fas fa-list"></i>  ' .
Html::encode('Lista volontari attivati') . 
'</h3> ';

$cols = [
        [   
            'label' => 'Num. elenco territoriale',
            'attribute' => 'num_elenco_territoriale',
            'width' => '190px'
        ],
        [   
            'label' => 'Attivazione data',
            'attribute' => 'creazione',
            'value' => function($model) {
                return \Yii::$app->formatter->asDate($model->creazione);
            }
        ],
        [   
            'label' => 'Attivazione ora',
            'attribute' => 'creazione',
            'value' => function($model) {
                return \Yii::$app->formatter->asTime($model->creazione);
            }
        ],
        [   
            'label' => 'Protocollo evento',
            'attribute' => 'protocollo_evento'
        ],
        [   
            'label' => 'Protocollo fronte',
            'attribute' => 'protocollo_fronte'
        ],
        [   'label' => 'Tipologia',
            'attribute' => 'id_tipologia',
            'width' => '190px',
            'filter'=> Html::activeDropDownList($volontariSearchModel, 'id_tipologia', \common\models\UtlEvento::getNestedFilterTipologie(), ['class' => 'form-control','prompt' => 'Tutti']),
            'format' => 'raw',
            'value' => function($data){
                return $data->tipologia;
            }
        ],
        [   'label' => 'Sottotipologia',
            'attribute' => 'id_sottotipologia',
            'width' => '190px',
            'filter'=> Html::activeDropDownList($volontariSearchModel, 'id_sottotipologia', \common\models\UtlEvento::getNestedFilterTipologie(), ['class' => 'form-control','prompt' => 'Tutti']),
            'format' => 'raw',
            'value' => function($data){
                return $data->sottotipologia;
            }
        ],
        [
            'label' => 'Comune',
            'attribute' => 'comune',
            'contentOptions' => ['style'=>'width: 300px;'],
            'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(
                \common\models\LocComune::find()
                ->where([
                            Yii::$app->params['region_filter_operator'], 
                            'id_regione', 
                            Yii::$app->params['region_filter_id']
                        ])
                ->orderBy([
                    'comune'=>SORT_ASC, 
                ])
                ->all(), 'comune', 'comune'),
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'multiple' => true,
                    'allowClear'=>true,
                ]
            ]
        ],
        [
            'label' => 'Provincia',
            'attribute' => 'provincia',
            'width' => '50px',
            'hAlign' => GridView::ALIGN_CENTER,
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
            ]
        ],
        [   
            'label' => 'Nome',
            'attribute' => 'nome'
        ],
        [   
            'label' => 'Cognome',
            'attribute' => 'cognome'
        ],
        [   
            'label' => 'Codice fiscale',
            'attribute' => 'codfiscale'
        ],
        [   
            'label' => 'Chiusura data',
            'attribute' => 'chiusura',
            'value' => function($model) {
                return (empty($model->chiusura)) ? "" : \Yii::$app->formatter->asDate($model->chiusura);
            }
        ],
        [   
            'label' => 'Chiusura ora',
            'attribute' => 'chiusura',
            'value' => function($model) {
                return (empty($model->chiusura)) ? "" : \Yii::$app->formatter->asTime($model->chiusura);
            }
        ],
        [   
            'label' => 'Associazione di volontario',
            'attribute' => 'denominazione'
        ],
        [   
            'label' => 'Località',
            'attribute' => 'localita'
        ],
        [
            'label' => 'Stato attivazione',
            'attribute' => 'stato',
            'contentOptions' => ['style'=>'width: 300px;'],
            'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
            'filter' => \common\models\UtlIngaggio::getStati(),
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'multiple' => true,
                    'allowClear'=>true,
                ]
            ],
            'value' => function($data) {
                return $data->statoLabel();
            }
        ],
        [
            'label' => 'Mezzo',
            'attribute' => 'full_mezzo'
        ],
        [
            'label' => 'Datore di lavoro',
            'attribute' => 'dt',
            'value' => function($data) {
                return @$data->datore_di_lavoro['denominazione'];
            }
        ],
        [
            'label' => 'Codice fiscale e/o partita iva',
            'attribute' => 'dt',
            'value' => function($data) {
                return @$data->datore_di_lavoro['cfpiva'];
            }
        ],
        [
            'label' => 'Localita',
            'attribute' => 'dt',
            'value' => function($data) {
                return @$data->datore_di_lavoro['via'] . " " . @$data->datore_di_lavoro['civico'] . " " . @$data->datore_di_lavoro['comune'] . " ".@$data->datore_di_lavoro['pr'];
            }
        ],
        [
            'label' => 'Rimborso',
            'attribute' => 'refund',
            'value' => function($data) {
                return $data->refund ? 'Si' : 'No';
            }
        ],
    ];
?>


<?= GridView::widget([
    'id' => 'lista-volontari-attivazioni',
    'dataProvider' => $volontariDataProvider,
    'filterModel' => $volontariSearchModel,
    'responsive'=>true,
    'hover'=>true,
    'perfectScrollbar' => true,
    'perfectScrollbarOptions' => [],
    'export' => false,
    'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
    'panel' => [
        'heading'=> $heading . ExportMenu::widget([
        'dataProvider' => $volontariDataProvider,
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
    'pjax'=>true,
    'pjaxSettings'=>[
        'neverTimeout'=> true,
    ],
    'columns' => $cols,
]); ?>