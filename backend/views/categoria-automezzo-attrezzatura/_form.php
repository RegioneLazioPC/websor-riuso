<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

use common\models\UtlTipologia;

/* @var $this yii\web\View */
/* @var $model common\models\UtlCategoriaAutomezzoAttrezzatura */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-categoria-automezzo-attrezzatura-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>

    
    <?= 
    $form->field($model, 'id_tipo_evento', ['options' => ['class'=>'']])->widget(Select2::classname(), [
        'data' => ArrayHelper::map( UtlTipologia::find()->where('idparent is null')->all(), 'id', 'tipologia'),
        'options' => [
            'placeholder' => 'Tipo evento...'
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
