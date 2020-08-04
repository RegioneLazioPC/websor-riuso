<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AlmAllertaMeteo */

$this->title = 'Crea allerta meteo';
$this->params['breadcrumbs'][] = ['label' => 'Allerte meteo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alm-allerta-meteo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
