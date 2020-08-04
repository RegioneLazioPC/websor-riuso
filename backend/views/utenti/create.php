<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlUtente */

$this->title = 'Creazione Nuovo Utente';
$this->params['breadcrumbs'][] = ['label' => 'Lista Utenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-utente-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
