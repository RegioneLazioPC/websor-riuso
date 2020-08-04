<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlAttrezzatura */

$this->title = $model->modello;
$this->params['breadcrumbs'][] = ['label' => 'Attrezzature', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-attrezzatura-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('updateAttrezzatura') && empty($model->id_sync)) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if(Yii::$app->user->can('deleteAttrezzatura') && empty($model->id_sync)) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sei sicuro di voler eliminare questo elemento?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Tipo',
                'attribute' =>'tipo',
                'value' => $model->tipo->descrizione
            ],
            'classe',
            'sottoclasse',
            'modello',
            'capacita',
            'unita',
            [
                'label' => 'Organizzazione',
                'attribute' =>'idorganizzazione',
                'value' => ($model->organizzazione) ? $model->organizzazione->denominazione : '-'
            ],
            [
                'label' => 'Sede',
                'attribute' =>'idsede',
                'value' => ($model->sede) ? $model->sede->tipo . " - " . $model->sede->indirizzo : '-'
            ],
            [
                'label' => 'Automezzo',
                'attribute' =>'idautomezzo',
                'value' => ($model->automezzo) ? $model->automezzo->targa : '-'
            ],
        ],
    ]) ?>

</div>
