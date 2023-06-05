<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\RichiestaCanadair */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="layers-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'layer_name')->textInput()->label('Nome da assegnare al layer') ?>

    <?= $form->field($model, 'srid')->textInput()->label('SRID * (necessario per una corretta visualizzazione)') ?>

    <?php echo $form->field($model, 'shapefile',['options' => ['class'=>'col-lg-12 no-pl no-pr']])
    	->widget(FileInput::classname(), [
    		'options' => [
    			'accept' => '.zip',
    			'multiple'=>false
    		],
    		'pluginOptions' => [
		        'showPreview' => false,
		        'showCaption' => true,
		        'showRemove' => true,
		        'showUpload' => false,
                'maxFileSize'=>\api\utils\Functions::convertPHPSizeToBytes()
		    ]
    	])->label('Carica file zip con archivio shapefile * (MAX: '.\api\utils\Functions::file_upload_max_size().')'); ?>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
