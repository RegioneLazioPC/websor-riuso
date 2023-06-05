<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\models\cap\CapResources;

/* @var $this yii\web\View */
/* @var $model common\models\RubricaGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rubrica-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo Html::errorSummary($model, ['encode' => false]); ?>

    <?php echo $form->field($model, 'identifier')->textInput(['maxlength' => true])->label('Identificatore') ?>
    <?php echo $form->field($model, 'url_feed_rss')->textInput(['maxlength' => true])->label('Url feed rss') ?>
    <?php echo $form->field($model, 'url_feed_atom')->textInput(['maxlength' => true])->label('Url feed atom') ?>
    <?php echo $form->field($model, 'expiry')->textInput(['type'=>'number'])->label('Ore per la scadenza') ?>

    <?php 
    $dial = [];
    foreach (CapResources::$avaible_profiles as $record) {
    	$dial[$record] = $record;
    }
    echo $form->field($model, 'profile', ['options' => ['class'=>'col-lg-12 no-pr no-pl']])->dropDownList(
        $dial,
        [
            
        ]
    )->label('Profilo'); ?>

    <?php 
    $feeds = [];
    foreach (CapResources::$selectable_feeds as $record) {
    	$feeds[$record] = $record;
    }
    echo $form->field($model, 'preferred_feed', ['options' => ['class'=>'col-lg-12 no-pr no-pl']])->dropDownList(
        $feeds,
        [
            
        ]
    )->label('Feed da utilizzare'); ?>

    <?php echo $form->field($model, 'raggruppamento')->textInput(['maxlength' => true])->label('Raggruppamento') ?>

    <?php 
    $auths = [];
    foreach (CapResources::$avaible_autentications as $record) {
    	$auths[$record] = $record;
    }
    echo $form->field($model, 'autenticazione', ['options' => ['class'=>'col-lg-12 no-pr no-pl', 'id'=>'autenticazione_input']])->dropDownList(
        $auths,
        [
            
        ]
    )->label('Autenticazione?'); ?>

    <?php echo $form->field($model, 'username')->textInput(['maxlength' => true])->label('Username') ?>
    <?php echo $form->field($model, 'password')->passwordInput()->label('Password') ?>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
