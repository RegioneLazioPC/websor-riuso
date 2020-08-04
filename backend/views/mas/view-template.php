<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MasMessageTemplate */

$this->title = $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Templates', 'url' => ['index-template']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mass-message-template-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= (Yii::$app->user->can('updateMasTemplate')) ? Html::a('Aggiorna', ['update-template', 'id' => $model->id], ['class' => 'btn btn-primary']) : "" ?>
        <?= (Yii::$app->user->can('deleteMasTemplate')) ? Html::a('Elimina', ['delete-template', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler eliminare questo elemento?',
                'method' => 'post',
            ],
        ]) : "" ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nome',
            'mail_body:raw',// NON SICURO
            'sms_body',
            'push_body',
            'fax_body:raw',// NON SICURO
            [
                'label'=>'Creazione',
                'attribute'=>'created_at',
                'format'=>'datetime'
            ],
            [
                'label'=>'Ultima modifica',
                'attribute'=>'updated_at',
                'format'=>'datetime'
            ]
        ],
    ]) ?>

</div>
