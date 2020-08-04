<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlAttrezzaturaTipo */

$this->title = 'Aggiungi tipo di attrezzatura';
$this->params['breadcrumbs'][] = ['label' => 'Tipo attrezzatura', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-attrezzatura-tipo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
