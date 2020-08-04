<?php
use common\models\ComComunicazioni;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\UtlIngaggio */
/* @var $form yii\widgets\ActiveForm */
if(!$canadair->engaged) $canadair->engaged = 0;
?>
<div>
    <div class="row">
    	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div id="error-msg" class="alert-error alert fade in hide">Errore aggiornamento DOS, verificare i campi.</div>
    	</div>
    </div>
    <div class="row m5w m20h bg-grayLighter box_shadow">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
            <div class="utl-update-canadair-form">

                <?php $form = ActiveForm::begin([
                    'action' =>['update-canadair', 'id' => $canadair->id],
                    'id' => 'formUpdateCanadair'
                ]); ?>


                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($canadair, 'engaged')->radioList(
                            [
                                0=>'No', 
                                true=> 'Si'
                            ])->label('Ingaggiato'); ?>
                       
                    </div>
                    <div class="col-md-12">
                        <?php echo $form->field($canadair, 'codice_canadair')->textInput()->label('Codice Canadair'); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo $form->field($canadair, 'motivo_rifiuto')->textarea(['rows' => '10'])->label('Note'); ?>
                    </div>
                </div>

                <div class="form-group">
                    
                    <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>
