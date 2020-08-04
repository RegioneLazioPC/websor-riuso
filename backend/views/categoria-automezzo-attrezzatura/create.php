<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlCategoriaAutomezzoAttrezzatura */

$this->title = 'Aggiungi categoria';
$this->params['breadcrumbs'][] = ['label' => 'Categoria automezzo/attrezzatura', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-categoria-automezzo-attrezzatura-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
