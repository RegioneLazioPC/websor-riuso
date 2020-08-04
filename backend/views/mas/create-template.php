<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MasMessageTemplate */

$this->title = 'Crea Template';
$this->params['breadcrumbs'][] = ['label' => 'Template', 'url' => ['index-template']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mass-message-template-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-template', [
        'model' => $model,
    ]) ?>

</div>
