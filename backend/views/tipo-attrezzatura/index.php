<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlAttrezzaturaTipoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipi di attrezzatura';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-attrezzatura-tipo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('createTipoAttrezzatura')) echo Html::a('Aggiungi tipo di attrezzatura', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'descrizione',
            [   'label' => 'Raggruppamenti',
                'value' => function($data){
                    $agg = [];
                    $ct = $data->getAggregatori()->all();
                    foreach ($ct as $c) {
                        $agg[] = $c->descrizione;
                    }
                    return implode(", ", $agg);
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => (Yii::$app->user->can('deleteTipoAttrezzatura')) ? '{view} {update} {delete}' : '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('viewTipoAttrezzatura')){
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio tipo attrezzatura'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateTipoAttrezzatura')){
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica tipo attrezzatura'),
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
