<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RichiestaMezzoAereo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="richiesta-mezzo-aereo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tipo_intervento')->dropDownList([ 'Soppressione' => 'Soppressione', 'Rico-Armata' => 'Rico-Armata', 'Ricognizione' => 'Ricognizione', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'priorita_intervento')->textInput() ?>

    <?= $form->field($model, 'tipo_vegetazione')->textInput() ?>

    <?= $form->field($model, 'area_bruciata')->textInput() ?>

    <?= $form->field($model, 'area_rischio')->textInput() ?>

    <?= $form->field($model, 'fronte_fuoco_num')->textInput() ?>

    <?= $form->field($model, 'fronte_fuoco_tot')->textInput() ?>

    <?= $form->field($model, 'elettrodotto')->dropDownList([ 'Non definito' => 'Non definito', 'Nessuno' => 'Nessuno', 'Da disattivare' => 'Da disattivare', 'A distanza di sicurezza' => 'A distanza di sicurezza', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'oreografia')->dropDownList([ 'Non definito' => 'Non definito', 'Pianura' => 'Pianura', 'Collina' => 'Collina', 'Montagna' => 'Montagna', 'Impervia' => 'Impervia', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'vento')->dropDownList([ 'Non definito' => 'Non definito', 'Nessuno' => 'Nessuno', 'Debole' => 'Debole', 'Moderato' => 'Moderato', 'Forte' => 'Forte', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'ostacoli')->dropDownList([ 'Non definito' => 'Non definito', 'Nessuno' => 'Nessuno', 'Infrastrutture' => 'Infrastrutture', 'Abitazioni' => 'Abitazioni', 'Fili a sbalzo - Teleferiche' => 'Fili a sbalzo - Teleferiche', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'cfs')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sigla_radio_dos')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'squadre')->textInput() ?>

    <?= $form->field($model, 'operatori')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
