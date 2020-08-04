<?php

use common\models\UtlSalaOperativa;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\models\UtlAnagrafica;
$anagrafica = ($model->anagrafica) ? $model->anagrafica : new UtlAnagrafica();
if(Yii::$app->request->post('UtlAnagrafica')) $anagrafica->load(Yii::$app->request->post());

$roles = [];
foreach (Yii::$app->authManager->getRoles() as $role => $detail) {
    $roles[$role] = $role;
}

/* @var $this yii\web\View */
/* @var $model common\models\UtlOperatorePc */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-operatore-pc-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($anagrafica, 'matricola')->textInput(['maxlength' => true]) ?>

    <?= $form->field($anagrafica, 'nome')->textInput(['maxlength' => true]) ?>

    <?= $form->field($anagrafica, 'cognome')->textInput(['maxlength' => true]) ?>

    <?= $form->field($anagrafica, 'email')->textInput(['maxlength' => true]) ?>

    <?php
    echo $form->field($model, 'ruolo')->widget(Select2::classname(), [
        'data' => $roles,
        'options' => [
            'placeholder' => 'Seleziona ruolo...',
            'value' => !empty($model->ruolo) ? $model->ruolo : 'Operatore',
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?= $form->field($model, 'username', ['options' => ['class' => 'text-primary']])->textInput(['maxlength' => true, 'value' => @$model->user->username]) ?>

    <?= $form->field($model, 'password', ['options' => ['class' => 'text-primary']])->passwordInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Crea' : 'Aggiorna', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
