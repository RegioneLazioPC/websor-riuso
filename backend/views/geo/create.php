<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RichiestaCanadair */

$this->title = 'Aggiungi layer';
$this->params['breadcrumbs'][] = ['label' => 'Layers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="richiesta-canadair-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
