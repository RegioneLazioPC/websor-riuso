<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlSalaOperativa */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-sala-operativa-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome')->textInput() ?>

    <?= $form->field($model, 'url_endpoint')->textInput() ?>

    <?= $form->field($model, 'api_auth_url')->textInput() ?>

    <?= $form->field($model, 'api_username')->textInput() ?>

    <?= $form->field($model, 'api_password')->textInput() ?>

    <div class="form-group">
        <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Crea' : 'Modifica', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>