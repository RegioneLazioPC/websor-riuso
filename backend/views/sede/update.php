<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VolSede */

$this->title = 'Aggiorna sede: '.$model->indirizzo;
$this->params['breadcrumbs'][] = ['label' => 'Sedi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Aggiorna';
?>
<div class="vol-sede-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->can('updateSede') && empty($model->id_sync)) echo  $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
