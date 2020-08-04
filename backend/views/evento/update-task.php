<?php

use common\models\UtlFunzioniSupporto;
use common\models\UtlSquadraOperativa;
use common\models\UtlTask;
use common\models\UtlTipologia;
use kartik\widgets\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */
$this->title = 'Gestione Evento Num. Protocollo: '.$evento->num_protocollo;
$this->params['breadcrumbs'][] = ['label' => 'Lista eventi calamitosi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="utl-evento-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            
            <strong>Operatore:</strong> <?php echo Html::encode($utente->anagrafica->nome);?> <?php echo Html::encode($utente->anagrafica->cognome);?>

            <?= $form->field($model, 'idevento')->hiddenInput()->label(false); ?>
            <?= $form->field($model, 'idoperatore')->hiddenInput()->label(false); ?>

            <?php
            echo $form->field($model, 'idfunzione_supporto')->widget(Select2::classname(), [
                'data' => ArrayHelper::map( UtlFunzioniSupporto::find()->all(), 'id', 'descrizione'),
                'options' => [
                    'placeholder' => 'Seleziona una funzione di supporto...',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Funzioni di supporto metodo Augustus:');
            ?>

            <?php
            echo $form->field($model, 'idtask')->widget(Select2::classname(), [
                'data' => ArrayHelper::map( UtlTask::find()->all(), 'id', 'descrizione'),
                'options' => [
                    'placeholder' => 'Seleziona ente...',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Enti da contattare:');
            ?>

            <?php
            echo $form->field($model, 'idsquadra')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(UtlSquadraOperativa::find()->all(), 'id', 'nome'),
                'options' => [
                    'placeholder' => 'Seleziona ente...',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Squadra operativa:');
            ?>

            <?= $form->field($model, 'note')->textarea(); ?>

        </div>
    </div>

    <div class="form-group">
        <?php echo Html::a('Annulla', ['index'], ['class'=>'btn btn-default']); ?>
        <?= Html::submitButton($model->isNewRecord ? 'Salva' : 'Aggiorna', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
