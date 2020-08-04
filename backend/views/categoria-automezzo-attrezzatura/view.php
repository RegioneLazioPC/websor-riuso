<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlCategoriaAutomezzoAttrezzatura */

$this->title = $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Categoria automezzo/attrezzatura', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-categoria-automezzo-attrezzatura-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('updateCategoria')) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if(Yii::$app->user->can('deleteCategoria')) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
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
            'descrizione',
            [
                'label' => 'Tipo evento',
                'attribute' =>'id_tipo_evento',
                'value' => ($model->tipoEvento) ? $model->tipoEvento->tipologia : ''
            ],
        ],
    ]) ?>

</div>
