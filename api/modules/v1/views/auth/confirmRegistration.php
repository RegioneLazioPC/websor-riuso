<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

?>
<style>
    body{
        background: #004367;
        color: white;
    }
    .confirm{
        text-align: center;
        margin-top: 10%;
    }
</style>
<div class="confirm">
    <p>
        <img src="<?php echo Url::base().'/v1/media/image/logo.png';?>" width="100" alt="">
    </p>
    <h1>APP PROTEZIONE CIVILE</h1>

    <?php if(!empty($error)): ?>
        <div class="row">
            <div class="col-lg-5">
                <h2><?php echo $error; ?></h2>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-5">
                <h2>Utenza confermata, procedere con il login</h2>
            </div>
        </div>
    <?php endif; ?>
</div>
