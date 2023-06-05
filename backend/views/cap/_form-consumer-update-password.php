<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\models\LocComuneGeom;
use common\models\cap\CapResources;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\RubricaGroup */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="rubrica-group-form">

    <?php $form = ActiveForm::begin(); ?>


    <?php echo Html::errorSummary($model, ['encode' => false]); ?>

    <?php echo $form->field($model, 'password')->passwordInput(['maxlength' => true])->label('Nuova password') ?>
    
    
    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
