<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VolVolontario */

$this->title = 'Aggiungi Volontario';
$this->params['breadcrumbs'][] = ['label' => 'Volontari', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-volontario-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->can('createVolontario')) echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
