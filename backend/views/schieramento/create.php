<?php

use yii\helpers\Html;


$this->title = 'Creazione nuovo schieramento';
$this->params['breadcrumbs'][] = ['label' => 'Lista Schieramenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-sala-operativa-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>