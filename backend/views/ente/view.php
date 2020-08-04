<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\helpers\Url;

use yii\data\ActiveDataProvider;
use common\models\ente\ConEnteContatto;
use common\models\ente\EntEnteSede;

/* @var $this yii\web\View */
/* @var $model common\models\VolOrganizzazione */

$this->title = 'Ente '.$model->denominazione;
$this->params['breadcrumbs'][] = ['label' => 'Enti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-organizzazione-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('Admin')) echo Html::a('Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Tipo',
                'attribute' =>'tipo',
                'value' => !empty($model->tipoEnte->descrizione) ? $model->tipoEnte->descrizione : ''
            ],
            'denominazione',
            'id_sync',
            [
                'label' => 'Strategia di aggiornamento',
                'attribute' =>'update_zona_allerta_strategy',
                'value' => \common\models\ZonaAllertaStrategy::getStrategyLabel( $model->update_zona_allerta_strategy )
            ],
            [
                'label' => 'Zone di allerta',
                'attribute' =>'zone_allerta'
            ]  
        ],
    ]) ?>

    <?php 

    $sediDataProvider = new ActiveDataProvider([
        'query' => EntEnteSede::find()->where(['id_ente'=>$model->id]),
        'pagination' => false
    ]);

    echo GridView::widget([
        'dataProvider' => $sediDataProvider,
        'responsive'=>true,
        'hover'=>true,
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'panel' => [
            'heading'=>'<h2 class="panel-title">Sedi</h2>',
            
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'indirizzo',
            [
                'label' => 'Comune',
                'attribute' => 'nome_comune',
                'value' => function($model, $key, $index, $column) {
                    return $model->locComune->comune;
                }
            ],
            [
                'label' => 'Tipo',
                'attribute' => 'tipo',
                'value' => function($model, $key, $index, $column) {
                    return $model->tipo == 0 ? 'Sede legale' : 'Sede operativa';
                }
            ]   
        ],
    ]); ?>



    <?php 
    $contattiDataProvider = new ActiveDataProvider([
        'query' => ConEnteContatto::find()->where(['id_ente'=>$model->id]),
        'pagination' => false
    ]);
    echo GridView::widget([
        'dataProvider' => $contattiDataProvider,
        'responsive'=>true,
        'hover'=>true,
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'panel' => [
            'heading'=>'<h2 class="panel-title">Recapiti</h2>',            
            
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Recapito',
                'attribute' => 'id',
                'value' => function($model, $key, $index, $column) {
                    return $model->contatto->contatto;
                }
            ],
            'note',
            [
                'label' => 'Tipo',
                'attribute' => 'use_type',
                'value' => function($model, $key, $index, $column) {
                    $use = 'Recapito generico';
                    switch($model->use_type) {
                        case 0:
                        return "Recapito per messaggistica";
                        break;
                        case 1:
                        return "Recapito per ingaggio";
                        break;
                        case 2:
                        return "Recapito per allertamento";
                        break;
                    }
                }
            ]
        ],
    ]); ?>



    <?php 

    echo $this->render('/everbridge/index', ['model'=>$model]);

    ?>

</div>
