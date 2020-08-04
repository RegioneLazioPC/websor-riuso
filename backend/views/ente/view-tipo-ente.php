<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\VolTipoOrganizzazione */

$this->title = $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Tipi di enti', 'url' => ['tipo-ente']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-tipo-organizzazione-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('Admin')) echo  Html::a('Aggiorna', ['update-tipo-ente', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'descrizione',
            [
                'label' => 'Strategia di aggiornamento',
                'attribute' =>'update_zona_allerta_strategy',
                'value' => \common\models\ZonaAllertaStrategy::getStrategyLabel( $model->update_zona_allerta_strategy )
            ],
        ],
    ]) ?>

</div>
