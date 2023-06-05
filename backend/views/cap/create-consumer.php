<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlSegnalazione */

$this->title = 'Crea nuovo consumer';
$this->params['breadcrumbs'][] = ['label' => 'Lista consumer CAP', 'url' => ['conumers']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-segnalazione-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-consumer', [
        'model' => $model
    ]); ?>

</div>
