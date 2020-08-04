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
    <div class="row m5w m20h bg-grayLighter box_shadow">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
            <div class="utl-canadair-form">

                <?php $form = ActiveForm::begin([
                    'action' =>['evento/send-mail-canadair'],
                    'id' => 'sendmailcanadair'
                ]); ?>


                <div class="row">
                    <div class="col-md-6">
                        <?php echo $form->field($comunicazione, 'contatto')->textInput()->label('Email'); ?>
                    </div>

                    <div class="col-md-12">
                        <label>OGGETTO</label>
                        <p>Richiesta Canadair - Evento <?php echo @$evento->num_protocollo; ?></p>
                        <?php echo $form->field($comunicazione, 'oggetto')->hiddenInput(['value'=>'Richiesta Canadair - Evento '. @$evento->num_protocollo])->label(false); ?>
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
                    <?php echo Html::submitButton('<i class="fas fa-send"></i> Invia', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
                
            </div>
        </div>        
    </div>
</div>

<?php
$js = '$("#sendmailcanadair").on("beforeSubmit", function(e) {
	e.preventDefault();
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: formData,
        success: function (response) {
            if(response.code == 200){
                $("#error-msg").addClass("hide");
                $("#modal-canadair").find("input,textarea,select").val("").end();
                $("#modal-canadair").modal("hide");
            }else{
                $("#error-msg").removeClass("hide");  
            }
        },
        error: function (e) {
            console.log("errore", e);
            $("#error-msg").removeClass("hide");
        }
    });
}).on("submit", function(e){
    e.preventDefault();
});';
$this->registerJs($js, $this::POS_READY);
?>

