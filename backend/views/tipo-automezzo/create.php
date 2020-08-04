<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlAutomezzoTipo */

$this->title = 'Aggiungi un tipo di automezzo';
$this->params['breadcrumbs'][] = ['label' => 'Tipi di automezzo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-automezzo-tipo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
