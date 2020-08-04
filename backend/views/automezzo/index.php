<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;


use common\models\UtlCategoriaAutomezzoAttrezzatura;
use common\models\UtlAutomezzoTipo;

use common\models\tabelle\TblTipoRisorsaMeta;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlAutomezzoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Automezzi';
$this->params['breadcrumbs'][] = $this->title;




$cols = [
    ['class' => 'yii\grid\SerialColumn'],
    'id'
];


$array_filters = [];
if(!empty(Yii::$app->request->get('meta'))){
    foreach (Yii::$app->request->get('meta') as $meta_key => $meta_filter) {
        if(!empty($meta_filter)) $array_filters[$meta_key] = $meta_filter;
    }
}


$meta_to_show = TblTipoRisorsaMeta::find()->where(['show_in_column'=>1])->all();
foreach ($meta_to_show as $meta) {
    $cols[] = 
        [
            'label' => $meta->label,
            'attribute' => '_meta',
            'filter'=> Html::textInput(
                'meta['.$meta->key.']', 
                @$array_filters[$meta->key],
                [
                    'class' => 'form-control',
                ]
            ),
            'format' => 'raw',
            'value' => function($model) use ($meta) {
                try {
                    return $model->meta[$meta->key];
                } catch( \Exception $e ) {
                    return null;
                }
            }
        ];
}


$cols = array_merge($cols, [
    'targa',
    [   'label' => 'Data immatricolazione',
        'attribute' => 'data_immatricolazione',
        'filterType' => GridView::FILTER_DATE,
        'filterWidgetOptions' => [
            'type' => 1,
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'autoclose' => true,
                'todayHighlight' => true,
            ]
        ]
    ],
    [
        'label' => 'Modello',
        'attribute' => 'modello',
        'format' => 'raw',
        'value' => function($data){
            return '<span style="max-width: 200px; display: block; white-space: pre-wrap;">'.$data['modello'].'</span>';
        }
    ],
    //'classe',
    //'sottoclasse',            
    [
        'label' => 'Tipo',
        'attribute' => 'idtipo',
        'filter'=> Html::activeDropDownList($searchModel, 'idtipo', ArrayHelper::map(UtlAutomezzoTipo::find()->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control','prompt' => 'Tutti']),
        'value' => function($data) {
            return $data['tipo']['descrizione'];
        }
    ],
    [
        'label' => 'Org.',
        'attribute' => 'org',
        'format' => 'raw',
        'value' => function($data){
            return '<span style="max-width: 200px; display: block; white-space: pre-wrap;">'.$data['organizzazione']['denominazione'].'</span>';
        }
    ],
    
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => (Yii::$app->user->can('deleteAutomezzo')) ? '{view} {update} {delete}' : '{view} {update}',
        'buttons' => [
            'view' => function ($url, $model) {
                if(Yii::$app->user->can('viewAutomezzo')){
                    return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                        'title' => Yii::t('app', 'Dettaglio automezzo'),
                        'data-toggle'=>'tooltip'
                    ]) ;
                }else{
                    return '';
                }
            },
            'update' => function ($url, $model) {
                if(Yii::$app->user->can('updateAutomezzo')){
                    return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                        'title' => Yii::t('app', 'Modifica automezzo'),
                        'data-toggle'=>'tooltip'
                    ]) ;
                }else{
                    return '';
                }
            }
        ]
    ]
]);






?>
<div class="utl-automezzo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <p>
        <?php if(Yii::$app->user->can('createAutomezzo')) echo Html::a('Aggiungi automezzo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'columns' => $cols
    ]); ?>
</div>
