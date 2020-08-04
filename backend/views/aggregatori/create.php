<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlAggregatoreTipologie */

$this->title = 'Crea aggregatore';
$this->params['breadcrumbs'][] = ['label' => 'Aggregatori tipologie', 'url' => ['index']];
$this->params['breadcrumbs'][] = Html::encode($this->title);
?>
<div class="utl-aggregatore-tipologie-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
