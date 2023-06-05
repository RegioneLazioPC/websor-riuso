<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlCategoriaAutomezzoAttrezzatura */

$this->title = $model->stringa_tipo_evento;
$this->params['breadcrumbs'][] = ['label' => 'Mappature', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cap-mappatura">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('editCapMap')) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if(Yii::$app->user->can('editCapMap')) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sei sicuro di voler cancellare questo elemento?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'stringa_tipo_evento',
            [
                'label'=>'Tipologia',
                'attribute'=>'eventType.tipologia'
            ],
            [
                'label'=>'Sottotipologia',
                'attribute'=>'eventSubType.tipologia'
            ]
        ],
    ]) ?>

</div>
