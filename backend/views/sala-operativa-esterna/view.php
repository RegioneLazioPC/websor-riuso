<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlSalaOperativa */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Lista Sale Comunali', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-sala-operativa-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php 
        if(Yii::$app->user->can('updateSalaOperativaEsterna')) echo Html::a('Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?php 
        if(Yii::$app->user->can('deleteSalaOperativaEsterna')) echo Html::a('Cancella', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler eliminare questo elemento?',
                'method' => 'post',
            ],
        ]); ?>
        <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nome',
            'url_endpoint',
            'api_auth_url',
            'api_username',
            'api_password'
        ],
    ]) ?>

</div>