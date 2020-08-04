<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VolSede */

$this->title = 'Aggiungi sede';
$this->params['breadcrumbs'][] = ['label' => 'Sedi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-sede-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->can('createSede')) echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
