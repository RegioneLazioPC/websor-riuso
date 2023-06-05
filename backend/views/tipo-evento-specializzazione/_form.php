<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use common\models\UtlTipologia;
use common\models\TblSezioneSpecialistica;
/* @var $this yii\web\View */
/* @var $model common\models\UtlAggregatoreTipologie */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-aggregatore-tipologie-form">

    <?php $form = ActiveForm::begin(); ?>

    
    <?= 
    $form->field($model, 'id_utl_tipologia', ['options' => ['class'=>'']])->widget(Select2::classname(), [
        'data' => ArrayHelper::map( UtlTipologia::find()->orderBy(['tipologia'=>SORT_ASC])->where('idparent is null')->all(), 'id', 'tipologia'),
        'options' => [
            'placeholder' => 'Tipo evento...'
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);

    ?>
    <?= 
    $form->field($model, 'id_tbl_sezione_specialistica', ['options' => ['class'=>'']])->widget(Select2::classname(), [
        'data' => ArrayHelper::map( TblSezioneSpecialistica::find()->orderBy(['descrizione'=>SORT_ASC])->all(), 'id', 'descrizione'),
        'options' => [
            'placeholder' => 'Specializzazione...'
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
