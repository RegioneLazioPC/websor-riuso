<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlIngaggio */

$this->title = 'Aggiorna Ingaggio';
$this->params['breadcrumbs'][] = ['label' => 'Ingaggi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="utl-ingaggio-update">

    <p>
        <?php if(Yii::$app->user->can('viewEvento')) echo Html::a('Dettagli evento', ['evento/view', 'id'=>$model->idevento], ['class' => 'btn btn-default']);?>
        <?php if(Yii::$app->user->identity->multipleCan(['createTaskEvento', 'updateTaskEvento', 'createIngaggio', 'updateIngaggio',
                            'createRichiestaCanadair', 'createRichiestaElicottero', 'createRichiestaDos',
                            'updateRichiestaCanadair', 'updateRichiestaElicottero', 'updateRichiestaDos'])) echo Html::a('Gestione evento', ['evento/gestione-evento', 'idEvento'=>$model->idevento], ['class' => 'btn btn-success']);?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'idevento',
            [
                'label'  => 'Organizzazione',
                'value'  => ($model->organizzazione) ? $model->organizzazione->denominazione : ""
            ],
            [
                'label'  => 'Sede',
                'value'  => ($model->sede) ? $model->sede->indirizzo . " - " . $model->sede->tipo : ""
            ],
            [
                'label'  => 'Automezzo',
                'value'  => ($model->automezzo) ? $model->automezzo->tipo->descrizione . " - " . $model->automezzo->targa : ""
            ],
            [
                'label'  => 'Attrezzatura',
                'value'  => ($model->attrezzatura) ? $model->attrezzatura->tipo->descrizione : ""
            ],
            'note:ntext',
            [
                'label'  => 'Stato',
                'value'  => $model->getStato()
            ],
            [
                'label'  => 'Data',
                'value'  => $model->created_at
            ],
            [
                'label'  => 'Chiusura',
                'value'  => $model->closed_at
            ],
        ],
    ]) ?>

    <?= $this->render('_update_form', [
        'model' => $model,
    ]) ?>


    <?= $this->render('_ingaggio_volontari', [
        'model' => $model,
        'dataProvider' => $dataProvider,
        'searchModel' => $searchModel
    ]) ?>

</div>
