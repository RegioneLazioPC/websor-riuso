<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MasMessage */

$this->title = 'Aggiorna messaggio: '.Yii::$app->formatter->asDate($model->created_at);
$this->params['breadcrumbs'][] = ['label' => 'Messaggi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Aggiorna';
?>
<div class="mass-message-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
