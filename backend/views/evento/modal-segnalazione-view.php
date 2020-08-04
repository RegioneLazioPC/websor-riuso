<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlSegnalazione */

$this->title = 'N. Protocollo ' . $model->num_protocollo;
?>
<div class="utl-segnalazione-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-12">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'label' => 'Segnalatore',
                        'attribute' =>'idutente',
                        'value' => (!empty($model->utente) && !empty($model->utente->anagrafica) && !empty($model->utente->anagrafica->nome)) ? $model->utente->anagrafica->nome.' '.$model->utente->anagrafica->cognome :  ""
                    ],
                    [
                        'label' => 'Tipologia Evento',
                        'attribute' =>'tipologia_evento',
                        'value' => $model->tipologia->tipologia
                    ],
                    'fonte',
                    'indirizzo',
                    'lat',
                    'lon',
                    'dataora_segnalazione',
                    'note:ntext'
                ],
            ]) ?>

        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="form-group">
                <?php echo Html::a('Visualizza scheda completa', ['segnalazione/view','id'=>$model->id], ['class' => 'btn btn-default', 'target' => '_blank']) ?>
            </div>
        </div>

    </div>

</div>
