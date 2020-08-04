<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\RichiestaMezzoAereo */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Richiesta Mezzo Aereos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="richiesta-mezzo-aereo-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            'tipo_intervento',
            'priorita_intervento',
            'tipo_vegetazione',
            'area_bruciata',
            'area_rischio',
            'fronte_fuoco_num',
            'fronte_fuoco_tot',
            'elettrodotto',
            'oreografia',
            'vento',
            'ostacoli',
            'note:ntext',
            'cfs',
            'sigla_radio_dos',
            'squadre',
            'operatori',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
