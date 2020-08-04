<?php

use yii\helpers\Html;
use kartik\grid\GridView;

use yii\helpers\ArrayHelper;
use common\models\UtlCategoriaAutomezzoAttrezzatura;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlAggregatoreTipologieSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Aggregatori tipologie';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-aggregatore-tipologie-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('createAggregatore')) echo Html::a('Crea Tipologia', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'descrizione',
            [   'label' => 'Categoria',
                'attribute' => 'id_categoria',
                //'filter'=>ArrayHelper::map(UtlTipologia::find()->asArray()->all(), 'id', 'tipologia'),
                'filter'=> Html::activeDropDownList($searchModel, 'id_categoria', ArrayHelper::map(UtlCategoriaAutomezzoAttrezzatura::find()->asArray()->all(), 'id', 'descrizione'), 
                    ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data){
                    return ($data->categoria) ? $data->categoria->descrizione : "";
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => (Yii::$app->user->can('deleteAggregatore')) ? '{view} {update} {delete}' : '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('viewAggregatore')){
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio aggregatore'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateAggregatore')){
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica aggregatore'),
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
