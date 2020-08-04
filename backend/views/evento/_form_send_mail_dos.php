<?php


use common\models\ComComunicazioni;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\UtlIngaggio */
/* @var $form yii\widgets\ActiveForm */

?>
<div>
    <div class="row">
    	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div id="error-msg" class="alert-error alert fade in hide">Errore invio mail, verificare i campi.</div>
    	</div>
    </div>
    <div class="row m5w m10h bg-grayLighter box_shadow">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
            <div class="utl-ingaggio-form">

                <?php $form = ActiveForm::begin([
                    'action' =>['evento/send-mail-dos'],
                    'id' => 'sendmaildos'
                ]); ?>

                <div class="row">
                    <div class="col-md-6">
                        <?php echo $form->field($comunicazione, 'contatto')->textInput()->label('EMAIL'); ?>
                    </div>
                    <div class="col-md-12">
                        <label>OGGETTO</label>
                        <p>Richiesta DOS - Evento <?php echo @$evento->num_protocollo; ?></p>
                        <?php echo $form->field($comunicazione, 'oggetto')->hiddenInput(['value'=>'Richiesta DOS - Evento '. @$evento->num_protocollo])->label(false); ?>
                    </div>
                    <div class="col-md-12">
                        <label>CONTENUTO</label>
                        <p>
                            Evento num. protocollo: <?php echo @$evento->num_protocollo; ?><br>
                            Tipologia: <?php echo @$evento->tipologia->tipologia; ?>
                            &nbsp;Sotto Tipologia: <?php echo @$evento->sottotipologia->tipologia; ?><br>
                            Comune: <?php echo @$evento->comune->comune; ?>
                            &nbsp;Indirizzo: <?php echo @$evento->indirizzo; ?><br>
                            Data e ora creazione evento: <?php echo @Yii::$app->formatter->asDatetime($evento->dataora_evento); ?>
                        </p>
                        <?php echo $form->field($comunicazione, 'contenuto')->textarea(['rows' => '5'])->label('EVENTUALI NOTE AGGIUNTIVE'); ?>
                    </div>
                </div>

                <?php echo $form->field($evento, 'id')->hiddenInput()->label(false); ?>

                <div class="form-group">
                    <?= Html::submitButton('<i class="fas fa-send"></i> Invia', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
                
            </div>
        </div>        
    </div>
</div>
