<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RubricaGroup */

$this->title = 'Crea nuovo gruppo';
$this->params['breadcrumbs'][] = ['label' => 'Gruppi rubrica', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rubrica-group-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
