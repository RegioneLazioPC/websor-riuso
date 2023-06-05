<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlSegnalazione */

$this->title = 'Aggiorna Risorsa CAP';
$this->params['breadcrumbs'][] = ['label' => 'Lista risorse CAP', 'url' => ['risorse']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-segnalazione-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-risorsa', [
        'model' => $model
    ]) ?>

</div>
