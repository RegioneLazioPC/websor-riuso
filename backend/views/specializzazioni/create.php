<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlSpecializzazione */

$this->title = 'Crea specializzazione';
$this->params['breadcrumbs'][] = ['label' => 'Specializzazioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-specializzazione-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
