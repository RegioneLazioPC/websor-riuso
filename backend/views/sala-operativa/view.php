<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlSalaOperativa */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Lista Sale Operative', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-sala-operativa-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancella', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nome',
            'indirizzo',
            'comune',
            'tipo',
        ],
    ]) ?>

</div>
