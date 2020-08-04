<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MasMessageTemplate */

$this->title = 'Aggiorna template messaggi: '.$model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Templates', 'url' => ['index-template']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view-template', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Aggiorna';
?>
<div class="mass-message-template-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-template', [
        'model' => $model,
    ]) ?>

</div>
