<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlAggregatoreTipologie */

$this->title = $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Aggregatori tipologie', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-aggregatore-tipologie-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('updateAggregatore')) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if(Yii::$app->user->can('deleteAggregatore')) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
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
            'descrizione',
            [
                'label' => 'Categoria',
                'attribute' =>'id_categoria',
                'value' => ($model->categoria) ? $model->categoria->descrizione : ''
            ],
        ],
    ]) ?>

</div>
