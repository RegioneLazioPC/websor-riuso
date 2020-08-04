<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlSquadraOperativa */

$this->title = 'Crea Squadra Operativa';
$this->params['breadcrumbs'][] = ['label' => 'Lista Squadre', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-squadra-operativa-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
