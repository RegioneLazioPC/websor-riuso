<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlCategoriaAutomezzoAttrezzatura */

$this->title = 'Aggiungi mappatura cap';
$this->params['breadcrumbs'][] = ['label' => 'Mappatura', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cap-mappatura-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
