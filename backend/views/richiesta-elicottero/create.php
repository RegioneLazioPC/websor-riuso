<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RichiestaElicottero */

$this->title = 'Create Richiesta Elicottero';
$this->params['breadcrumbs'][] = ['label' => 'Richiesta Elicotteros', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="richiesta-elicottero-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
