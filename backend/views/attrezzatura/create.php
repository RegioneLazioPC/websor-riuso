<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlAttrezzatura */

$this->title = 'Aggiungi attrezzatura';
$this->params['breadcrumbs'][] = ['label' => 'Attrezzature', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-attrezzatura-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->can('createAttrezzatura')) echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
