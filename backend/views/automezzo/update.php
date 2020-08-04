<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlAutomezzo */

$this->title = 'Automezzo: '.$model->targa;
$this->params['breadcrumbs'][] = ['label' => 'Automezzi', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="utl-automezzo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->can('updateAutomezzo') && empty($model->id_sync)) echo  $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
