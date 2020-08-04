<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MasMessage */

$this->title = 'Crea nuovo messaggio';
$this->params['breadcrumbs'][] = ['label' => 'Messaggi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mass-message-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_full-form', [
        'model' => $model,
    ]) ?>

</div>
