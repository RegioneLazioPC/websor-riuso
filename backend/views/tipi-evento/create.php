<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlTipologia */

$this->title = 'Crea tipo evento';
$this->params['breadcrumbs'][] = ['label' => 'Tipi evento', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-tipologia-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
