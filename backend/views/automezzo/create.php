<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlAutomezzo */

$this->title = 'Aggiungi automezzo';
$this->params['breadcrumbs'][] = ['label' => 'Automezzi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-automezzo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if(Yii::$app->user->can('createAutomezzo')) echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
