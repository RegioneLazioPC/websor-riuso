<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VolTipoOrganizzazione */

$this->title = 'Crea tipo organizzazione';
$this->params['breadcrumbs'][] = ['label' => 'Tipo Organizzazione', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-tipo-organizzazione-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
