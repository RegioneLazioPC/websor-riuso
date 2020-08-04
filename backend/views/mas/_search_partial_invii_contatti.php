<?php

use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEventoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="con_mas_invio_contact-search">

    <?php 
    $form = ActiveForm::begin([
        'action' => [ 'view-invio', 'id_invio' => $model->id_invio ],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?php echo $form->field($model, 'group')->radioList( [
                    0=>'No', 
                    1 => 'Si'
                ], [
                    'item' => function($index, $label, $name, $checked, $value) use ($model) {
                        $selected = ($model->group == $value) ? 'checked' : '';
                        $return = '<label class="modal-radio" style="margin-right: 12px">';
                        $return .= '<input type="radio"  name="' . $name . '" value="' . $value . '" '. $selected . ' style="margin-right: 8px;">';
                        $return .= '<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';

                        return $return;
                    }
                ] )->label('Raggruppa per destinatario');

            ?>
        </div>
        <div class="col-md-6">
            <?php echo $form->field($model, 'sent')->radioList( [
                    null => 'Tutti',
                    -1=>'Non inviati', 
                    1 => 'Solo inviati'
                ], [
                    'item' => function($index, $label, $name, $checked, $value) use ($model) {
                        $selected = ($model->sent == $value) ? 'checked' : '';
                        $return = '<label class="modal-radio" style="margin-right: 12px">';
                        $return .= '<input type="radio"  name="' . $name . '" value="' . $value . '" '. $selected . ' style="margin-right: 8px;">';
                        $return .= '<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';

                        return $return;
                    }
                ] )->label('Mostra inviati');

            ?>
        </div>
    </div>
    

    <div class="form-group col-lg-4">
        <?= Html::submitButton('Cerca', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Annulla', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
