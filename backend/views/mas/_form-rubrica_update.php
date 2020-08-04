<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;
use dosamigos\tinymce\TinyMce;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

use common\models\ViewRubrica;
use common\models\LocComune;
/* @var $this yii\web\View */
/* @var $model common\models\MasMessageTemplate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mass-message-template-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model); ?>

    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="p20w p10h m5w m20h bg-grayLighter box_shadow">
                <h3>Dati anagrafici</h3>
                <?= $form->field($anagrafica, 'nome')->textInput(['maxlength' => true]) ?>
                <?= $form->field($anagrafica, 'cognome')->textInput(['maxlength' => true]) ?>
                <?= $form->field($anagrafica, 'codfiscale')->textInput(['maxlength' => 16]) ?>
                <?= $form->field($anagrafica, 'luogo_nascita')->textInput()->label("Luogo di nascita") ?>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="p20w p10h m5w m20h bg-grayLighter box_shadow">
            <h3>Dati rubrica</h3>
                <?= $form->field($model, 'dettagli')->textArea() ?>
                <?= $form->field($model, 'ruolo', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                    'data' => ViewRubrica::getTipiRiferimento(),
                    'options' => [
                        'placeholder' => 'Seleziona il tipo di contatto...',
                        'ng-model' => 'ctrl.contatto_type',
                        'ng-init' => "ctrl.contatto_type = '".$model->ruolo."'"
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
                <div class="row">
                    <div class="col-sm-6 p10w"><?= $form->field($model, 'lat')->textInput(['maxlength' => true]) ?></div>
                    <div class="col-sm-6 p10w"><?= $form->field($model, 'lon')->textInput(['maxlength' => true]) ?></div>
                </div>
                
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="p20w p10h m5w m20h bg-grayLighter box_shadow">
                <h3>Indirizzo</h3>
                <?= $form->field($indirizzo, 'indirizzo')->textInput(['maxlength' => true]) ?>
                <?= $form->field($indirizzo, 'civico')->textInput() ?>
                <?= $form->field($indirizzo, 'cap')->textInput(['maxlength' => 5, 'minlength'=>5]) ?>
                <?= $form->field($indirizzo, 'id_comune', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                            'data' => ArrayHelper::map( LocComune::find()->where(
                                [
                                    Yii::$app->params['region_filter_operator'], 
                                    'id_regione', 
                                    Yii::$app->params['region_filter_id']
                                ])->orderBy(['comune'=>SORT_ASC])->all(), 'id', 'comune'),
                            'options' => [
                                'placeholder' => 'Seleziona un comune...',
                                'ng-model' => 'ctrl.comune',
                                'ng-init' => "ctrl.comune = '".$indirizzo->id_comune."'"
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('Comune');
                        ?>
            </div>
        </div>
    </div>

    
    

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
