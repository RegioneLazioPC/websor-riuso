<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlTipoEventoSpecializzazioneTipologie */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tipo evento/specializzazione', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-aggregatore-tipologie-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('updateTipoEventoSpecializzazione')) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if(Yii::$app->user->can('deleteTipoEventoSpecializzazione')) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
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
            [
                'label' => 'Tipo evento',
                'attribute' =>'id_utl_tipologia',
                'value' => ($model->tipologia) ? $model->tipologia->tipologia : ''
            ],
            [
                'label' => 'Specializzazione',
                'attribute' =>'id_tbl_sezione_specialistica',
                'value' => ($model->specializzazione) ? $model->specializzazione->descrizione : ''
            ],
        ],
    ]) ?>

</div>
