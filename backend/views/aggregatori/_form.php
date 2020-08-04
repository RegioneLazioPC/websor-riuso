<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use common\models\UtlCategoriaAutomezzoAttrezzatura;
/* @var $this yii\web\View */
/* @var $model common\models\UtlAggregatoreTipologie */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-aggregatore-tipologie-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>

    <?= 
    $form->field($model, 'id_categoria', ['options' => ['class'=>'']])->widget(Select2::classname(), [
        'data' => ArrayHelper::map( UtlCategoriaAutomezzoAttrezzatura::find()->all(), 'id', 'descrizione'),
        'options' => [
            'placeholder' => 'Categoria...'
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);

    ?>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
