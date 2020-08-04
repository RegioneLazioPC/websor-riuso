<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\UtlIngaggio;
/* @var $this yii\web\View */
/* @var $model common\models\UtlIngaggio */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-ingaggio-form">

    

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'stato', ['options'=>[
            'class'=>'no-p',            
            ]])->dropDownList([ 
            '0' => 'Non confermato', 
            '1' => 'Confermato', 
            '2' => 'Rifiutato', 
            '3' => 'Chiuso'
            ], ['prompt' => '']) ?>

    <?= $form->field($model, 'motivazione_rifiuto', ['options'=>[
            'class'=>'no-p',            
            'style'=>'display: none'
            ]])->dropDownList(UtlIngaggio::getMotivazioniRifiuto(), ['prompt' => '']) ?>

    <?= $form->field($model, 'motivazione_rifiuto_note', ['options'=>[
            'style'=>'display: none'
            ]])->textarea(['rows' => 6]) ?>
            

    <div class="form-group">
        <?= Html::submitButton('Aggiorna', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php
    if(Yii::$app->user->can('adminPermissions')){
        echo Html::a('Cancella', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler eliminare questo elemento? Questa azione è irreversibile',
                'method' => 'post',
            ],
        ]);
    }
    ?>

</div>
<?php 
$js = '

    var start_val = $("#utlingaggio-stato").val();

    if(start_val == 2) {
        $(".field-utlingaggio-motivazione_rifiuto").show();
        var child_val = $("#utlingaggio-motivazione_rifiuto").val();
        if(child_val == 5) {
            $(".field-utlingaggio-motivazione_rifiuto_note").show();
        }
    }

    $("#utlingaggio-stato").on(\'change\', function() {
        if($("#utlingaggio-stato").val() == 2){
            $(".field-utlingaggio-motivazione_rifiuto").show();
        } else {
            $(".field-utlingaggio-motivazione_rifiuto").hide();
            $(".field-utlingaggio-motivazione_rifiuto_note").hide();
        }
    })

    $("#utlingaggio-motivazione_rifiuto").on(\'change\', function() {        
        if($("#utlingaggio-motivazione_rifiuto").val() == 5){
            $(".field-utlingaggio-motivazione_rifiuto_note").show();
        } else {
            $(".field-utlingaggio-motivazione_rifiuto_note").hide();
        }
    })
';
$this->registerJs($js, $this::POS_READY);


