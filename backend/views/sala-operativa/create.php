<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlSalaOperativa */

$this->title = 'Creazione Nuova Sala Operativa';
$this->params['breadcrumbs'][] = ['label' => 'Lista Sale Operative', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-sala-operativa-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
