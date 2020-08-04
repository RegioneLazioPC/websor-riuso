<?php

use yii\helpers\Html;

use yii\web\NotFoundHttpException;

/* @var $this yii\web\View */
/* @var $model common\models\AlmAllertaMeteo */

$this->title = 'Aggiorna allerta meteo: '.Yii::$app->formatter->asDate($model->data_allerta);
$this->params['breadcrumbs'][] = ['label' => 'Allerte meteo', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Aggiorna';
?>
<div class="alm-allerta-meteo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
