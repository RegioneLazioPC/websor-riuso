<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlSegnalazione */

$this->title = 'Crea Nuova Segnalazione Emergenza';
$this->params['breadcrumbs'][] = ['label' => 'Lista segnalazioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-segnalazione-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'utente' => $utente,
        'showLatLon' => $showLatLon
    ]) ?>

</div>
