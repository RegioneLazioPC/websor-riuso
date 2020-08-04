<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlOperatorePc */

$this->title = 'Creazione nuovo utente WEB SOR';
$this->params['breadcrumbs'][] = ['label' => 'Utenti Web SOR', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-operatore-pc-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
