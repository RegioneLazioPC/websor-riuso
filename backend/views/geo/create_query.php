<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RichiestaCanadair */

$this->title = 'Aggiungi query';
$this->params['breadcrumbs'][] = ['label' => 'Query geografiche', 'url' => ['index-query']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="richiesta-canadair-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_query', [
        'model' => $model,
    ]) ?>

</div>
