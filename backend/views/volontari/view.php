<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

use yii\data\ActiveDataProvider;
use common\models\ConVolontarioContatto;
/* @var $this yii\web\View */
/* @var $model common\models\VolVolontario */

$this->title = $model->anagrafica->nome . " " .$model->anagrafica->cognome;
$this->params['breadcrumbs'][] = ['label' => 'Volontari', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-volontario-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if(Yii::$app->user->can('updateVolontario') && empty($model->id_sync)) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if(Yii::$app->user->can('deleteVolontario') && empty($model->id_sync)) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler eliminare questo elemento?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'anagrafica.nome',
            'anagrafica.cognome',
            'anagrafica.codfiscale',
            'anagrafica.telefono',
            'anagrafica.email',
            'anagrafica.pec',     
            'anagrafica.indirizzo_residenza',     
            'anagrafica.cap_residenza',  
            'anagrafica.comuneResidenza.comune',            
            'ruolo',
            'spec_principale',
            'valido_dal',
            'valido_al',
            'operativo:boolean',
            [
                'label'  => 'Organizzazione',
                'value'  => ($model->organizzazione) ? $model->organizzazione->denominazione : ""
            ],
            [
                'label'  => 'Sede',
                'value'  => ($model->sede) ? $model->sede->indirizzo . " - " . $model->sede->tipo : ""
            ],
        ],
    ]) ?>

    
    
    <?php 
    $dataProvider = new ActiveDataProvider([
        'query' => ConVolontarioContatto::find()->where(['id_volontario'=>$model->id]),
        'pagination' => [
            'pageSize' => 50,
        ],
    ]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
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

</div>
