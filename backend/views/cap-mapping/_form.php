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

    <?= $form->field($model, 'stringa_tipo_evento')->textInput(['maxlength' => true]) ?>

    
    <?= $form->field($model, 'id_tipo_evento')->dropDownList(
        ArrayHelper::map(
        UtlTipologia::find()->where(['idparent'=>null])->all(), 'id', 'tipologia'
    ),           // Flat array ('id'=>'label')
        [
            // options
            'id' => 'tipo-id',
            'prompt'=>'Seleziona tipologia...'
        ]
    )->label('Tipologia'); ?>

    <?= $form->field($model, 'id_sottotipo_evento')->dropDownList(
        ArrayHelper::map(
        UtlTipologia::find()
        ->where('utl_tipologia.idparent is not null')
        ->joinWith('tipologiaGenitore as tipologia_genitore')
        ->orderBy(['tipologia_genitore.tipologia'=>SORT_ASC])->all(), 'id', function($model) {
            return $model->tipologiaGenitore->tipologia . " - " . $model->tipologia;
        }
    ),           // Flat array ('id'=>'label')
        [
            // options
            'id' => 'tipo-id',
            'prompt'=>'Seleziona tipologia...'
        ]
    )->label('Sottotipologia'); ?>

	<div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
