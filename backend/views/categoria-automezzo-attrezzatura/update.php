<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlCategoriaAutomezzoAttrezzatura */

$this->title = 'Aggiorna categoria: '.$model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Categoria attrezzatura/automezzo', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="utl-categoria-automezzo-attrezzatura-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
