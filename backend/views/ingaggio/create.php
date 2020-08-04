<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlIngaggio */

$this->title = 'Create Utl Ingaggio';
$this->params['breadcrumbs'][] = ['label' => 'Utl Ingaggios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-ingaggio-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
