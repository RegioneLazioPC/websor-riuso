<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VolSede */

$this->title = 'Aggiungi una sede';
$this->params['breadcrumbs'][] = ['label' => 'Lista Organizzazioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-sede-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_sede', [
        'model' => $model,
    ]) ?>

</div>
