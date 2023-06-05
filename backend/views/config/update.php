<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */

$this->title = 'Aggiorna chiave: ' . $key;
$this->params['breadcrumbs'][] = ['label' => 'Configurazioni applicativo', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Chiave: '.$key, 'url' => ['update', 'key' => $key]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="utl-evento-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'key'=>$key,
        'avaible_keys' => $avaible_keys
    ]) ?>

</div>
