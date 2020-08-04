<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\Url;

use dosamigos\tinymce\TinyMce;

use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model common\models\MasMessageTemplate */
/* @var $form yii\widgets\ActiveForm */



?>

<div class="mass-message-template-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>

    <h3>Tags</h3>

    <p>Utilizzando i seguenti tag è possibile inserire delle aree che verranno rimpiazzate dal rispettivo elemento all'atto di creazione del messaggio</p>
    <p>{{message}} - Verrà sostituito col messaggio</p>
    <p>{{data_allerta}} - Verrà sostituito con la data dell'allerta impostata in fase di creazione</p>

    <p><strong>IMPORTANTE!</strong> Nell'inserimento delle immagini utilizzare sempre un link esterno all'applicativo</p>
    <?php 

        echo Tabs::widget([
        'items' => [
            [
                'label' => 'Mail',
                'content' => $form->field($model, 'mail_body')->widget(TinyMce::className(), [
                    'options' => [],
                    'language' => 'it',
                    'clientOptions' => [
                        'plugins' => [
                            "advlist autolink lists link charmap print preview anchor",
                            "searchreplace visualblocks code fullscreen",
                            "insertdatetime table contextmenu paste",
                            "image imagetools spellchecker visualchars textcolor",
                            "autosave colorpicker hr nonbreaking template"
                        ],
                        'relative_urls' => false,
                        'remove_script_host' => false,
                        'document_base_url' => Url::base(true),
                        'toolbar1' => "undo redo | styleselect fontselect fontsizeselect forecolor backcolor | bold italic",
                        'toolbar2' => "alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                        'image_advtab' => true,
                        'image_title' => true,
                    ]
                ])->label('Template email'),
                'active' => true
            ],
            [
                'label' => 'Fax',
                'content' => $form->field($model, 'fax_body')->widget(TinyMce::className(), [
                    'options' => [],
                    'language' => 'it',
                    'clientOptions' => [
                        'plugins' => [
                            "advlist autolink lists link charmap print preview anchor",
                            "searchreplace visualblocks code fullscreen",
                            "insertdatetime table contextmenu paste", // media
                            "image imagetools spellchecker visualchars textcolor",
                            "autosave colorpicker hr nonbreaking template"
                        ],
                        'relative_urls' => false,
                        'remove_script_host' => false,
                        'document_base_url' => Url::base(true),
                        'toolbar1' => "undo redo | styleselect fontselect fontsizeselect forecolor backcolor | bold italic",
                        'toolbar2' => "alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                        'image_advtab' => true,
                        'image_title' => true,
                        
                    ]
                ])->label('Template fax'),
                'options' => ['id' => 'fax_body'],
            ],
            [
                'label' => 'Sms',
                'content' => $form->field($model, 'sms_body')->textarea(['rows' => 6,'maxlength' => true])->label('Template sms'),
                'options' => ['id' => 'sms_body'],
            ],
            [
                'label' => 'Push notifications',
                'content' => $form->field($model, 'push_body')->textarea(['rows' => 6,'maxlength' => true])->label('Notifiche push'),
                'options' => ['id' => 'push_body'],
            ],
        ],
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
