<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use yii\bootstrap\Tabs;
use dosamigos\tinymce\TinyMce;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

use common\models\AlmAllertaMeteo;
use common\models\MasMessageTemplate;
/* @var $this yii\web\View */
/* @var $model common\models\MasMessage */
/* @var $form yii\widgets\ActiveForm */

if(Yii::$app->request->get('id_allerta')) $model->id_allerta = Yii::$app->request->get('id_allerta');
?>

<div class="mass-message-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model); ?>
    <div class="row">
        <div class="col-sm-6">
        <?= 
            $form->field($model, 'id_template', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                'data' => ArrayHelper::map( MasMessageTemplate::find()->all(), 'id', 'nome'),
                'options' => [
                    'placeholder' => 'Template...',
                    'ng-model' => 'ctrl.template',
                    'ng-disabled' => 'ctrl.tp',
                    'ng-init' => "ctrl.template = '".$model->id_template."'"
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Seleziona template messaggio');

        ?>
        </div>
        <div class="col-sm-6">
        <?= 
            $form->field($model, 'id_allerta', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                'data' => ArrayHelper::map( AlmAllertaMeteo::find()->all(), 'id', 'id'),
                'options' => [
                    'placeholder' => 'Allerta meteo...',
                    'ng-model' => 'ctrl.allerta',
                    'ng-disabled' => 'ctrl.al',
                    'ng-init' => "ctrl.allerta = '".$model->id_allerta."'"
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])->label('Inserisci allerta meteo');

        ?>
        </div>
    </div>

    <div>
        <?= $form->field($model, 'title')->textInput([
                'label'=>'Titolo del messaggio'
            ]) ?>
    </div>

    <h3>Canale</h3>
    <p>Seleziona i canali da utilizzare</p>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'channel_mail')->checkbox([
                'label'=>'Email'
            ]) ?>
        </div>
        <div class="col-md-2">
        <?= $form->field($model, 'channel_pec')->checkbox([
            'label'=>'Pec'
        ]) ?>
        </div>
        <div class="col-md-2">
        <?= $form->field($model, 'channel_fax')->checkbox([
            'label'=>'Fax'
        ]) ?>
        </div>
        <div class="col-md-2">
        <?= $form->field($model, 'channel_sms')->checkbox([
            'label'=>'Sms'
        ]) ?>
        </div>
        <div class="col-md-2">
        <?= $form->field($model, 'channel_push')->checkbox([
            'label'=>'Push notification'
        ]) ?>
        </div>
    </div>

    <h3>Messaggio</h3>
    <p>Inserisci il messaggio, se hai selezionato un template il messaggio inserito qui andrà a sostituire la dicitura <strong>{{message}}</strong> del template</p>
    <?php 

        echo Tabs::widget([
        'items' => [
            [
                'label' => 'Mail',
                'content' => $form->field($model, 'mail_text')->widget(TinyMce::className(), [
                    'options' => [],
                    'language' => 'it',
                    'clientOptions' => [
                        'plugins' => [
                            "advlist autolink lists link charmap print preview anchor",
                            "searchreplace visualblocks code fullscreen",
                            "insertdatetime media table contextmenu paste",
                            "image imagetools spellchecker visualchars textcolor",
                            "autosave colorpicker hr nonbreaking template"
                        ],
                        'relative_urls' => false,
                        'remove_script_host' => false,
                        'toolbar1' => "undo redo | styleselect fontselect fontsizeselect forecolor backcolor | bold italic",
                        'toolbar2' => "alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                        'image_advtab' => true,
                        'image_title' => true,
                        
                    ]
                ])->label('Testo email'),
                'active' => true
            ],
            [
                'label' => 'Fax',
                'content' => $form->field($model, 'fax_text')->widget(TinyMce::className(), [
                    'options' => [],
                    'language' => 'it',
                    'clientOptions' => [
                        'plugins' => [
                            "advlist autolink lists link charmap print preview anchor",
                            "searchreplace visualblocks code fullscreen",
                            "insertdatetime media table contextmenu paste",
                            "image imagetools spellchecker visualchars textcolor",
                            "autosave colorpicker hr nonbreaking template"
                        ],
                        'relative_urls' => false,
                        'remove_script_host' => false,
                        'toolbar1' => "undo redo | styleselect fontselect fontsizeselect forecolor backcolor | bold italic",
                        'toolbar2' => "alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                        'image_advtab' => true,
                        'image_title' => true,
                        
                    ]
                ])->label('Testo fax'),
                'options' => ['id' => 'fax_text'],
            ],
            [
                'label' => 'Sms',
                'content' => $form->field($model, 'sms_text')->textarea(['rows' => 6,'maxlength' => true])->label('Testo sms'),
                'options' => ['id' => 'sms_text'],
            ],
            [
                'label' => 'Push notifications',
                'content' => $form->field($model, 'push_text')->textarea(['rows' => 6,'maxlength' => true])->label('Testo notifiche push'),
                'options' => ['id' => 'push_text'],
            ],
        ],
    ]);
    ?>

    <?= $form->field($model, 'note')->textarea(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
