<?php
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;

//??? @todo fix for postgres
//use sammaye\audittrail\AuditTrail;
use common\models\AuditTrailSearch;


use common\models\UtlSegnalazione;
use yii\helpers\Html;
use kartik\export\ExportMenu;



$filterModel = new AuditTrailSearch;
$dataProvider = $filterModel->search( Yii::$app->request->get(), 'common\models\UtlSegnalazione');




$cols = [
            [
                'label' => 'Operatore',
                'attribute' => 'nome',
                'value' => function($model, $index, $widget){

                    if($model->user && $model->user->utente) return $model->user->utente->anagrafica->cognome ." ".$model->user->utente->anagrafica->nome;
                    if($model->user && $model->user->operatore) return $model->user->operatore->anagrafica->cognome ." ".$model->user->operatore->anagrafica->nome;

                    return "Utente rimosso";
                }
            ],
            [
                'attribute' => 'num_protocollo',
                'label' => 'N.Protocollo',
                'value' => function($model, $index, $widget){
                    $segnalazione = UtlSegnalazione::findOne($model->model_id);
                    if(!empty($segnalazione)){
                        return $segnalazione->num_protocollo;
                    }else{
                        return true;
                    }
                }
            ],
            [
                'label' => 'Azione svolta',
                'attribute' => 'action',
                'value' => function($model, $index, $widget){
                    $action="";
                    switch ($model->action){
                        case 'CHANGE':
                            $action = 'Segnalazione modificata';
                            break;
                        case 'CREATE':
                            $action = 'Nuova segnalazione creata';
                            break;
                        case 'DELETE':
                            $action = 'Segnalazione cancellata';
                            break;
                        default:
                            $action = 'Modifica campo';
                            break;
                    }
                    return $action;
                }
            ],
            [
                'label' => 'Campo modificato',
                'attribute' => 'field',
                'value' => function($model, $index, $widget){
                    return $model->getParent()->getAttributeLabel($model->field);
                }
            ],        
            [
                'label' => 'Data modifica',
                'attribute' => 'stamp',
                'value' => function($model, $index, $widget){
                    return date("d-m-Y H:i:s", strtotime($model->stamp));
                }
            ]            
        ];






echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $filterModel,
    'responsive'=>true,
    'hover'=>true,
    'perfectScrollbar' => true,
    'perfectScrollbarOptions' => [],
    'toggleData'=>false,
    'export' => false,
    'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
    'panel' => [
        'heading'=> "Scarica " . ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $cols,
                'target' => ExportMenu::TARGET_BLANK,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false
                ]                
            ]),
        
    ],
    'columns' => [
        [
            'label' => 'Operatore',
            'attribute' => 'nome',
            'value' => function($model, $index, $widget){

                if($model->user && $model->user->utente) return $model->user->utente->anagrafica->cognome ." ".$model->user->utente->anagrafica->nome;
                if($model->user && $model->user->operatore) return $model->user->operatore->anagrafica->cognome ." ".$model->user->operatore->anagrafica->nome;

                return "Utente rimosso";
            }
        ],
        [
            'attribute' => 'num_protocollo',
            'label' => 'N.Protocollo',
            'value' => function($model, $index, $widget){
                $segnalazione = UtlSegnalazione::findOne($model->model_id);
                if(!empty($segnalazione)){
                    return $segnalazione->num_protocollo;
                }else{
                    return true;
                }
            }
        ],
        [
            'label' => 'Azione svolta',
            'attribute' => 'action',
            'filter'=> Html::activeDropDownList($filterModel, 'action', [
                'CHANGE' => 'Segnalazione modificata',
                'CREATE' => 'Nuova segnalazione creata',
                'SET' => 'Segnalazione modificata',
                'DELETE' => 'Segnalazione cancellata',
            ], ['class' => 'form-control','prompt' => 'Tutti']),
            'value' => function($model, $index, $widget){
                $action="";
                switch ($model->action){
                    case 'CHANGE':
                        $action = 'Segnalazione modificata';
                        break;
                    case 'CREATE':
                        $action = 'Nuova segnalazione creata';
                        break;
                    case 'DELETE':
                        $action = 'Segnalazione cancellata';
                        break;
                    default:
                        $action = 'Modifica campo';
                        break;
                }
                return $action;
            }
        ],
        [
            'label' => 'Campo modificato',
            'attribute' => 'field',
            'filter'=> Html::activeDropDownList($filterModel, 'field', (new UtlSegnalazione)->attributeLabels(), ['class' => 'form-control','prompt' => 'Tutti']),
            'value' => function($model, $index, $widget){
                return $model->getParent()->getAttributeLabel($model->field);
            }
        ],        
        [
            'label' => 'Data modifica',
            'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'type' => 1,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ],
            'attribute' => 'stamp',
            'value' => function($model, $index, $widget){
                return date("d-m-Y H:i:s", strtotime($model->stamp));
            }
        ]
    ]
]); ?>