<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VolOrganizzazione */

$this->title = 'Crea Nuova Organizzazione di Volontariato';
$this->params['breadcrumbs'][] = ['label' => 'Lista Organizzazioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-organizzazione-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
