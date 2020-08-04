<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MasMessageTemplate */

$this->title = 'Aggiorna record rubrica';
$this->params['breadcrumbs'][] = ['label' => 'Rubrica', 'url' => ['index-rubrica']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mass-message-template-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form-rubrica_update', [
        'model' => $model,
        'anagrafica' => $anagrafica,
        'indirizzo' => $indirizzo,
    ]) ?>

</div>
