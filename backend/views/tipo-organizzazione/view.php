<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\VolTipoOrganizzazione */

$this->title = $model->tipologia;
$this->params['breadcrumbs'][] = ['label' => 'Tipi Organizzazioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-tipo-organizzazione-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('updateTipoOrganizzazione')) echo  Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if(Yii::$app->user->can('deleteTipoOrganizzazione')) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sei sicuro di voler eliminare questo elemento?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tipologia',
            [
                'label' => 'Strategia di aggiornamento',
                'attribute' =>'update_zona_allerta_strategy',
                'value' => \common\models\ZonaAllertaStrategy::getStrategyLabel( $model->update_zona_allerta_strategy )
            ],
        ],
    ]) ?>

</div>
