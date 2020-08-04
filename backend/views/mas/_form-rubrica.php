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
                <?= $form->field($anagrafica, 'codfiscale')->textInput(['maxlength' => 16])->label('Codice fiscale *') ?>
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
                        'placeholder' => 'Seleziona il ruolo...',
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
    <?php 

    $js = "

    $(document).ready(function(){
        
        $('#utlcontatto-type').change(function() {
            
            var val = $(this).val();
            if(val == 2 || val == 4 ) {
              $('[name=\"UtlContatto[check_mobile]\"]').attr('disabled',false);
            } else {
                $('[name=\"UtlContatto[check_mobile]\"]').attr('disabled',true);
            }
        })
    })

    ";
    $this->registerJs($js, $this::POS_READY);
?>
    
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="p20w p10h m5w m20h bg-grayLighter box_shadow">
                <h3>Contatto (ne potrai aggiungere altri in seguito)</h3>
                <?= $form->field($contatto, 'type', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                            'data' => ViewRubrica::getTipi(),
                            'options' => [
                                'placeholder' => 'Seleziona il tipo di contatto...',
                                'ng-model' => 'ctrl.contatto_type',
                                'ng-init' => "ctrl.contatto_type = '".$contatto->type."'"
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('Tipo contatto');
                        ?>

                <?= $form->field($contatto, 'contatto')->textInput() ?>
                
                <?php echo $form->field($contatto, 'check_mobile')->radioList( [
                        0=>'No', 
                        1 => 'Si'
                    ], [
                        'item' => function($index, $label, $name, $checked, $value) use ($contatto) {
                            $selected = ($contatto->check_mobile == $value) ? 'checked' : '';
                            $return = '<label class="modal-radio" style="margin-right: 12px">';
                            $return .= '<input type="radio" disabled name="' . $name . '" value="' . $value . '" '. $selected . ' style="margin-right: 8px;">';
                            $return .= '<span>' . ucwords($label) . '</span>';
                            $return .= '</label>';

                            return $return;
                        }
                    ] )->label('Cellulare');
            
                ?>

                <?php echo $form->field($contatto, 'check_predefinito')->radioList( [
                        0=>'No', 
                        1 => 'Si'
                    ], [
                        'item' => function($index, $label, $name, $checked, $value) use ($contatto) {
                            $selected = ($contatto->check_predefinito == $value) ? 'checked' : '';
                            $return = '<label class="modal-radio" style="margin-right: 12px">';
                            $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" '. $selected . ' style="margin-right: 8px;">';
                            $return .= '<span>' . ucwords($label) . '</span>';
                            $return .= '</label>';

                            return $return;
                        }
                    ] )->label('Contatto predefinito');
            
                ?>

                <?php echo $form->field($contatto, 'use_type')->radioList( [
                        0=>'Messaggistica', 
                        2 => 'Allertamento'
                    ], [
                        'item' => function($index, $label, $name, $checked, $value) use ($contatto) {
                            $selected = ($contatto->use_type == $value) ? 'checked' : '';
                            $return = '<label class="modal-radio" style="margin-right: 12px">';
                            $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" '. $selected . ' style="margin-right: 8px;">';
                            $return .= '<span>' . ucwords($label) . '</span>';
                            $return .= '</label>';

                            return $return;
                        }
                    ] )->label('Messaggistica/allertamento');

                ?>
    
                <?= $form->field($contatto, 'note')->textArea() ?>
            </div>
        </div>
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
