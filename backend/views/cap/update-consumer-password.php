<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlSegnalazione */

$this->title = 'Aggiorna consumer CAP';
$this->params['breadcrumbs'][] = ['label' => 'Lista consumer CAP', 'url' => ['consumers']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-segnalazione-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-consumer-update-password', [
        'model' => $model
    ]) ?>

</div>
