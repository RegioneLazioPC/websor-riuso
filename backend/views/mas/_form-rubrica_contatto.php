<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;
use dosamigos\tinymce\TinyMce;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

use common\models\ViewRubrica;
use common\models\LocComune;
use common\models\utility\UtlContatto;
/* @var $this yii\web\View */
/* @var $model common\models\MasMessageTemplate */
/* @var $form yii\widgets\ActiveForm */
$contatto = new UtlContatto;

$js = "

    $(document).ready(function(){
        
        $('#utlcontatto-type').change(function() {
            
            var val = $(this).val();
            if(val == 2 || val == 4 ) {
              $('[name=\"UtlContatto[check_mobile]\"]').attr('disabled',false);
            } else {
                $('[name=\"UtlContatto[check_mobile]\"]').attr('disabled',true);
            }

            if(val == 6 ) {
              $('[name=\"UtlContatto[vendor]\"]').attr('disabled',false);
            } else {
                $('[name=\"UtlContatto[vendor]\"]').attr('disabled',true);
            }
        })
    })

    ";
    $this->registerJs($js, $this::POS_READY);
    
?>

<div class="mass-message-template-form">

    <?php $form = ActiveForm::begin([
        'action' =>['mas/add-contatto-rubrica', 'id'=>$id_model]
    ]); ?>
    
    
        
    <h3>Dati contatto</h3>
    <?= $form->field($contatto, 'contatto')->textInput() ?>
    <?= $form->field($contatto, 'type', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                'data' => ViewRubrica::getTipi(),
                'options' => [
                    'placeholder' => 'Seleziona il tipo di contatto...',
                    'ng-model' => 'ctrl.contatto_type',
                    'ng-init' => "ctrl.contatto_type = '".$contatto->type."'"
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Tipo contatto');
            ?>

    <?php echo $form->field($contatto, 'check_mobile')->radioList( [
            0=>'No', 
            1 => 'Si'
        ], [
            'item' => function($index, $label, $name, $checked, $value) use ($contatto) {
                $selected = ($contatto->check_mobile == $value) ? 'checked' : '';
                $return = '<label class="modal-radio" style="margin-right: 12px">';
                $return .= '<input type="radio" disabled name="' . $name . '" value="' . $value . '" '. $selected . ' style="margin-right: 8px;">';
                $return .= '<span>' . ucwords($label) . '</span>';
                $return .= '</label>';

                return $return;
            }
        ] )->label('Cellulare');

    ?>

    <?php echo $form->field($contatto, 'check_predefinito')->radioList( [
            0=>'No', 
            1 => 'Si'
        ], [
            'item' => function($index, $label, $name, $checked, $value) use ($contatto) {
                $selected = ($contatto->check_predefinito == $value) ? 'checked' : '';
                $return = '<label class="modal-radio" style="margin-right: 12px">';
                $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" '. $selected . ' style="margin-right: 8px;">';
                $return .= '<span>' . ucwords($label) . '</span>';
                $return .= '</label>';

                return $return;
            }
        ] )->label('Contatto predefinito');

    ?>

    <?php echo $form->field($contatto, 'use_type')->radioList( [
            0=>'Messaggistica', 
            2 => 'Allertamento'
        ], [
            'item' => function($index, $label, $name, $checked, $value) use ($contatto) {
                $selected = ($contatto->use_type == $value) ? 'checked' : '';
                $return = '<label class="modal-radio" style="margin-right: 12px">';
                $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" '. $selected . ' style="margin-right: 8px;">';
                $return .= '<span>' . ucwords($label) . '</span>';
                $return .= '</label>';

                return $return;
            }
        ] )->label('Messaggistica/allertamento');

    ?>

    <?php echo $form->field($contatto, 'vendor')->radioList( [
            'ios'=>'ios', 
            'android' => 'android'
        ], [
            'item' => function($index, $label, $name, $checked, $value) use ($contatto) {
                $selected = ($contatto->vendor == $value) ? 'checked' : '';
                $return = '<label class="modal-radio" style="margin-right: 12px">';
                $return .= '<input type="radio" disabled name="' . $name . '" value="' . $value . '" '. $selected . ' style="margin-right: 8px;">';
                $return .= '<span>' . ucwords($label) . '</span>';
                $return .= '</label>';

                return $return;
            }
        ] )->label('Vendor del dispositivo');

    ?>

    <?= $form->field($contatto, 'note')->textArea() ?>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
