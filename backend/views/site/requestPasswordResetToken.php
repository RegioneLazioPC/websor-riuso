<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Request password reset';
?>
<div class="login-box">
    <div class="login-logo">
        <p><?php echo Html::img('@web/images/logo.png', ['width'=>100]); ?></p>
        <a href="#" style="color: #fff">#Web<strong>SOR</strong></a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">RECUPERA PASSWORD</p>
        <p>Inserisci il tuo indirizzo email, ti verrà inviato il link per impostare una nuova password</p>
        <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Conferma', ['class' => 'btn btn-primary btn-block btn-flat']) ?>
            </div>

        <?php ActiveForm::end(); ?>
        <?= Html::a('Accedi', ['site/login']) ?><br />
    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->