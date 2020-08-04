<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\ViewRubrica;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;

use common\models\LocProvincia;
use common\models\LocComune;

use common\models\TblSezioneSpecialistica;
/* @var $this yii\web\View */
/* @var $searchModel common\models\MasMessageTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rubrica';
$this->params['breadcrumbs'][] = $this->title;


$cols = [
            [
                'attribute'=>'valore_riferimento',
                'label' => 'Riferimento'
            ],
            [
                'attribute'=>'valore_contatto',
                'label' => 'Contatto'
            ],
            [
                'attribute'=>'tipo_contatto',
                'width'=>'150px',
                'label' => 'Tipo contatto',
                'filter'=> Html::activeDropDownList($searchModel, 'tipo_contatto', ViewRubrica::getTipi(), ['class' => 'form-control','prompt' => 'Tutti']),
                'value'=>function($model) {
                    return $model->tipo();
                }
            ],
            [
                'label' => 'Comune',
                'attribute' => 'comune'                
            ],
            [
                'label' => 'Provincia',
                'attribute' => 'provincia',
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => array_merge([''=>'Tutti'], ArrayHelper::map(LocProvincia::find()
                    ->where([
                                Yii::$app->params['region_filter_operator'], 
                                'id_regione', 
                                Yii::$app->params['region_filter_id']
                            ])
                    ->all(), 'sigla', 'sigla')),
            ],
            [
                'attribute'=>'tipologia_riferimento',
                'label' => 'Tipo riferimento',
                'filter'=> Html::activeDropDownList($searchModel, 'tipologia_riferimento', ViewRubrica::getTipiRiferimento(), ['class' => 'form-control','prompt' => 'Tutti'])
            ],
            [
                'attribute'=>'specializzazione',
                'label' => 'Specializzazione',
                'filter'=> Html::activeDropDownList(
                    $searchModel, 
                    'specializzazione', 
                    ArrayHelper::map( TblSezioneSpecialistica::find()->all(), 'id', 'descrizione'), 
                    ['class' => 'form-control','prompt' => 'Tutti']
                ),
                'format'=>'html',   
                'contentOptions' => [
                    'style'=>'max-width:200px; min-height:100px; overflow: auto; word-wrap: break-word;'
                ],
                'value'=>function($model) {
                    if($model->tipologia_riferimento == 'organizzazione') {
                        return implode(", ", array_map(function($sezione){
                            return $sezione->descrizione;
                        }, $model->specializzazioni));
                    } else {
                        return '-';
                    }
                }
            ],
            [ 
                'attribute'=>'note',
                'label' => 'Note',
                'contentOptions' => [
                    'style'=>'max-width:200px; min-height:100px; overflow: auto; word-wrap: break-word;'
                ]
            ]
            
        ];

$actions = [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view-rubrica} {update-rubrica}',
                'buttons' => [
                    'view-rubrica' => function ($url, $model) {
                        
                        if(Yii::$app->user->can('listMasRubrica')){
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>&nbsp;&nbsp;', 
                                ['view-rubrica', 
                                'id_riferimento' => $model->id_riferimento, 
                                'tipo_riferimento' => $model->tipo_riferimento,
                            ], [
                                'title' => Yii::t('app', 'Dettagli'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update-rubrica' => function ($url, $model) {
                        
                        if(Yii::$app->user->can('updateMasRubrica') && $model->tipo_riferimento == 'id_mas_rubrica'){
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;', ['update-rubrica', 'id' => $model->id_riferimento], [
                                'title' => Yii::t('app', 'Modifica'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    }
                ],
            ];

$full_cols = $cols;
$full_cols[] = $actions;

?>
<div class="mass-message-template-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?= Html::a('Crea nuovo record', ['create-rubrica'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'id' => 'lista-rubrica',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive'=>true,
        'hover'=>true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'pjax'=>true,
        'pjaxSettings'=>[
            'neverTimeout'=> true,
        ],
        'panel' => [
            'before'=> Html::a('<i class="glyphicon glyphicon-repeat"></i> Azzera filtri', ['index-rubrica'], ['class' => 'btn btn-info m10w']),
            'heading'=> "Scarica rubrica completa " . ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $cols,
                'target' => ExportMenu::TARGET_BLANK,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false
                ]                
            ]),
            //'footer'=>true,
        ],
        'columns' => $full_cols
    ]); ?>
</div>
