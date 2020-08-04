<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <title>APP RESET PASSWORD</title>
<style>
    body{
        background: #004367;
        color: white;
        font-family: sans-serif;
    }
    .confirm{
        text-align: center;
        margin-top: 10%;
    }
    
    .control-label{
        display: block;
        text-transform: uppercase;
        font-size: 12px;
    }
    input{
            width: 350px;
        margin: 10px 0;
        line-height: 37px;
        border: none;
        font-size: 14px;
        padding: 0 12px;
    }
    button{
        border: none;
    line-height: 37px;
    padding: 0 20px;
    text-transform: uppercase;
    letter-spacing: 2px;
    background-color: #eb731d;
    color: #fff;
    cursor: pointer;
    }
    button:hover{
        background-color: #c55e14;
    }
</style>
</head>
<body>

    <div class="login-box" style="text-align: center">
        <p>
            <img src="<?php echo Url::base().'/v1/media/image/logo.png';?>" width="100" alt="">
        </p>
        <h1>APP PROTEZIONE CIVILE</h1>
        <div class="login-box-body">
            <p class="login-box-msg">RESET PASSWORD</p>
            <?php if(!$set) { ?> 
                <p>Inserisci la nuova password per il tuo account</p>
            <?php } ?>
            <?php 
            if(!$set) {
                $form = ActiveForm::begin(['id' => 'reset-password-form', 'method'=>'POST']); ?>
                <?= $form->field($model, 'password')
                ->passwordInput(['autofocus' => true]) ?>
                    <div class="form-group">
                        <?= Html::submitButton('Conferma', ['class' => 'btn btn-primary btn-block btn-flat']) ?>
                    </div>
                <?php ActiveForm::end(); 
            } else {
                ?>
                <p>Password modificata correttamente, apri l'app</p>
                <?php
            }
            ?>
        </div>
        <!-- /.login-box-body -->
    </div><!-- /.login-box -->
</body>
</html>