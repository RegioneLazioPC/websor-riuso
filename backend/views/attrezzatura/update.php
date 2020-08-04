<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlAttrezzatura */

$this->title = 'Aggiorna attrezzatura: '.$model->modello;
$this->params['breadcrumbs'][] = ['label' => 'Attrezzature', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="utl-attrezzatura-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->can('updateAttrezzatura') && empty($model->id_sync)) echo  $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
