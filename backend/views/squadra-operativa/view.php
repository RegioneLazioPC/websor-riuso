<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlSquadraOperativa */

$this->title = 'Dettaglio Squadra Operativa: '.$model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Squadra Operativas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-squadra-operativa-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancella', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nome',
            'caposquadra',
            'comune.comune',
            'numero_membri',
            'tel_caposquadra',
            'cell_caposquadra',
            'frequenza_tras',
            'frequenza_ric',
        ],
    ]) ?>

</div>
