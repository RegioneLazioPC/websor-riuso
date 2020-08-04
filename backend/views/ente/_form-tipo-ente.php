<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
/* @var $this yii\web\View */
/* @var $model common\models\VolTipoOrganizzazione */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vol-tipo-organizzazione-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= 
    $form->field($model, 'update_zona_allerta_strategy', ['options' => ['class'=>'']])->widget(Select2::classname(), [
        'data' => \common\models\ZonaAllertaStrategy::getStrategies(),
        'options' => [
            'placeholder' => 'Strategia di aggiornamento'
        ],
        'pluginOptions' => [
            'allowClear' => false
        ],
    ])->label("Strategia di aggiornamento");
    ?>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
