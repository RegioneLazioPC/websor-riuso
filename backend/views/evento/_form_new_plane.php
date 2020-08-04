<?php


use common\models\ComComunicazioni;
use common\models\RichiestaMezzoAereo;
use kartik\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\UtlIngaggio */
/* @var $form yii\widgets\ActiveForm */

$model = new RichiestaMezzoAereo();

?>
<div class="new-plane-form">
    <div class="row">
    	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="error-msg alert-error alert fade in hide">Errore invio dati</div>
    	</div>
        <?php $form = ActiveForm::begin([
            'action' =>['evento/new-plane'],
            'id' => 'newplane'
        ]); ?>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">

                    <h5 class="m10h text-uppercase color-gray">Intervento</h5>

                    <?= $form->field($model, 'dataora_inizio_missione', ['options' => ['class' => 'col-lg-3 no-p']])->widget(DateTimePicker::classname(), [
                        'options' => ['placeholder' => 'Data e ora inizio'],
                        'pluginOptions' => [
                            'format' => 'dd-mm-yyyy H:i:s',
                            'todayHighlight' => true,
                            'autoclose'=>true
                        ]
                    ]); ?>

                    <?= $form->field($model, 'dataora_fine_missione', ['options' => ['class' => 'col-lg-3 no-pr']])->widget(DateTimePicker::classname(), [
                        'options' => ['placeholder' => 'Data e ora fine'],
                        'pluginOptions' => [
                            'format' => 'dd-mm-yyyy H:i:s',
                            'todayHighlight' => true,
                            'autoclose'=>true
                        ]
                    ]); ?>

                    

                    <?= $form->field($model, 'tipo_intervento', ['options'=>['class'=>'col-lg-6']])->dropDownList(RichiestaMezzoAereo::getEnumFields('tipo_intervento'), ['prompt' => '']) ?>

                    <?= $form->field($model, 'tipo_vegetazione',  ['options'=>['class'=>'col-lg-6 no-p']])->textInput() ?>

                    <?= $form->field($model, 'area_bruciata', ['options'=>['class'=>'col-lg-3']])->textInput() ?>

                    <?= $form->field($model, 'area_rischio',  ['options'=>['class'=>'col-lg-3']])->textInput() ?>

                </div>
            </div>

            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">

                    <h5 class="m10h text-uppercase color-gray">Dettagli incendio</h5>

                    <?= $form->field($model, 'fronte_fuoco_num',  ['options'=>['class'=>'col-lg-6 no-p']])->textInput() ?>

                    <?= $form->field($model, 'fronte_fuoco_tot',  ['options'=>['class'=>'col-lg-6']])->textInput() ?>

                    <?= $form->field($model, 'elettrodotto', ['options'=>['class'=>'col-lg-6 no-p']])->dropDownList(RichiestaMezzoAereo::getEnumFields('elettrodotto'), ['prompt' => '']) ?>

                    <?= $form->field($model, 'oreografia', ['options'=>['class'=>'col-lg-6']])->dropDownList(RichiestaMezzoAereo::getEnumFields('oreografia'), ['prompt' => '']) ?>

                    <?= $form->field($model, 'vento',  ['options'=>['class'=>'col-lg-6 no-p']])->dropDownList(RichiestaMezzoAereo::getEnumFields('vento'), ['prompt' => '']) ?>

                    <?= $form->field($model, 'ostacoli',  ['options'=>['class'=>'col-lg-6']])->dropDownList(RichiestaMezzoAereo::getEnumFields('ostacoli'), ['prompt' => '']) ?>

                    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

                    <?php echo $form->field($evento, 'id')->hiddenInput()->label(false); ?>
                    <?php echo Html::hiddenInput('idingaggio', '', ['id' => 'idingaggio']) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>

        </div>
    </div>

</div>

<?php
$js = '$("#newplane").on("beforeSubmit", function(e) {
	e.preventDefault();
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: formData,
        success: function (response) {
            if(response.code == 200){
                $(".error-msg").addClass("hide");
                $("#modal-new-plane").find("input,textarea,select").val("").end();
                $("#modal-new-plane").modal("hide");
            }else{
                $(".error-msg").removeClass("hide");  
                console.log("errore", response.message);
            }
        },
        error: function (e) {
            console.log("errore", e);
            $(".error-msg").removeClass("hide");
        }
    });
}).on("submit", function(e){
    e.preventDefault();
});';
$this->registerJs($js, $this::POS_READY);
?>

