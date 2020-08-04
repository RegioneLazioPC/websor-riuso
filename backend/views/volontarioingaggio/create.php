<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ConVolontarioIngaggio */

$this->title = 'Create Con Volontario Ingaggio';
$this->params['breadcrumbs'][] = ['label' => 'Con Volontario Ingaggios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="con-volontario-ingaggio-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
