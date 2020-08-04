<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Reset password';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="login-box">
    <div class="login-logo">
        <p><?php echo Html::img('@web/images/logo.png', ['width'=>100]); ?></p>
        <a href="#" style="color: #fff">#Web<strong>SOR</strong></a>
    </div>
    <div class="login-box-body">
        <p class="login-box-msg">RESET PASSWORD</p>
        <p>Inserisci la nuova password per il tuo account</p>
        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

            <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Conferma', ['class' => 'btn btn-primary btn-block btn-flat']) ?>
            </div>

        <?php ActiveForm::end(); ?>

        <?= Html::a('Accedi', ['site/login']) ?><br />
    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
