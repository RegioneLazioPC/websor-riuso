<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RichiestaMezzoAereo */

$this->title = 'Create Richiesta Mezzo Aereo';
$this->params['breadcrumbs'][] = ['label' => 'Richiesta Mezzo Aereos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="richiesta-mezzo-aereo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
