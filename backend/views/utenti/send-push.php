<?php

use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlUtente */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Sistema notifiche allerta meteo';
?>

<div class="utl-utente-form">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    
    <div class="row">
    	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">

            <?= $form->field($model, 'message')->textarea(['maxlength' => true])->label('Invio notifica App EasyAlert') ?>

    	</div>
    </div>

    <div class="form-group">
        <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>
        <?php echo Html::submitButton('Invia', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
