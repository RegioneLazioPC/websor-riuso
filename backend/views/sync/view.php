<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\helpers\Url;

use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\VolSede */

$this->title = "ERROR SYNC";
$this->params['breadcrumbs'][] = ['label' => 'ERRORI SYNC', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-sede-view">

    <h1><?= Html::encode($this->title) ?></h1>
   

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'level',
            'service',
            'stack:raw',
            [
                'attribute'=>'created_at',
                'format' => 'datetime',
                'label' => 'Data e ora',
            ]
        ],
    ]) ?>


</div>
