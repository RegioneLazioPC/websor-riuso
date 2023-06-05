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

$list_comuni   = LocComuneGeom::find()->select('pro_com,comune')
    ->where(['cod_reg' => Yii::$app->params['region_filter_id']])
    ->orderBy(['comune'=>SORT_ASC])
    ->all();
$comuni = ArrayHelper::map( $list_comuni,'pro_com','comune');

?>

<div class="rubrica-group-form">

    <?php $form = ActiveForm::begin(); ?>


    <?php echo Html::errorSummary($model, ['encode' => false]); ?>

    <?php echo $form->field($model, 'username')->textInput(['maxlength' => true])->label('Username') ?>
    <?php echo $form->field($model, 'password')->passwordInput(['maxlength' => true])->label('Password') ?>
    <?php echo $form->field($model, 'address')->textInput(['maxlength' => true])->label('Address (* per destinatari messaggio cap)') ?>

    <?php 
    echo $form->field($model, 'enabled')            
         ->widget(Select2::classname(), [
        'data' => [0=>'No',1=>'Si'],
        'options' => ['placeholder' => 'Seleziona se abilitare il consumer'],
        'pluginOptions' => [
            'allowClear' => false
        ],
    ])->label('Abilitato');

    echo $form->field($model, 'sala_operativa')            
         ->widget(Select2::classname(), [
        'data' => [0=>'No',1=>'Si'],
        'options' => ['placeholder' => 'Seleziona se abilitare il consumer'],
        'pluginOptions' => [
            'allowClear' => false
        ],
    ])->label('Ha una sala operativa');

    echo $form->field($model, 'comuni')            
         ->widget(Select2::classname(), [
        'data' => $comuni,
        'options' => ['placeholder' => 'Scegli un comune ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'multiple' => true
        ],
    ]);
    ?>
    

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
