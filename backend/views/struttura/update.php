<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VolOrganizzazione */

$this->title = 'Modifica Struttura: ' . $model->denominazione;
$this->params['breadcrumbs'][] = ['label' => 'Lista Strutture', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Html::encode($model->denominazione), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="vol-organizzazione-update">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
