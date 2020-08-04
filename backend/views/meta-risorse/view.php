<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\UtlAttrezzaturaTipo */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => 'Meta risorsa', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-attrezzatura-tipo-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('updateTipoRisorsaMeta')) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if(Yii::$app->user->can('deleteTipoRisorsaMeta')) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler eliminare questo elemento?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'label',
            [
                'label' => 'Tipo',
                'attribute' => 'type',
                'value' => function($model) {
                    return $model->tipo();
                }
            ],
            [
                'label' => 'Mostra in colonna',
                'attribute' => 'show_in_column',
                'value' => function($model) {
                    return $model->inColumn();
                }
            ],
            [
                'label' => 'Opzioni',
                'attribute' => 'extra',
                'value' => function($model) {
                    return $model->extra();
                }
            ],
            'key',
            'ref_id',
            'id_sync',
        ],
    ]) ?>


</div>
