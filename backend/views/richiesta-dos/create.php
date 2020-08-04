<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RichiestaDos */

$this->title = 'Create Richiesta Dos';
$this->params['breadcrumbs'][] = ['label' => 'Richiesta Dos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="richiesta-dos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
