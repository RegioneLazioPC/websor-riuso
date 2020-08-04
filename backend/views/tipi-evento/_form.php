<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use common\models\UtlTipologia;
/* @var $this yii\web\View */
/* @var $model common\models\UtlTipologia */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-tipologia-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tipologia')->textInput(['maxlength' => true]) ?>

    <?= 
    $form->field($model, 'idparent', ['options' => ['class'=>'']])->widget(Select2::classname(), [
        'data' => ArrayHelper::map( UtlTipologia::find()->where('idparent is null')->all(), 'id', 'tipologia'),
        'options' => [
            'placeholder' => 'Genitore...'
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);

    ?>
    <?php 
    if(empty($model->idparent)) {
        echo $form->field($model, 'check_app')->radioList( [
                0=>'No', 
                1 => 'Si'
            ], [
                'item' => function($index, $label, $name, $checked, $value) use ($model) {
                    $selected = ($model->check_app == $value) ? 'checked' : '';
                    $return = '<label class="modal-radio" style="margin-right: 12px">';
                    $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" '. $selected . ' style="margin-right: 8px;">';
                    $return .= '<span>' . ucwords($label) . '</span>';
                    $return .= '</label>';

                    return $return;
                }
            ] )->label('Mostra in app');
    }
    ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
