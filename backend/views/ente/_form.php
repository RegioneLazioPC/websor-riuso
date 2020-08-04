<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\VolTipoOrganizzazione;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use kartik\widgets\DatePicker;


$now = new \DateTime();

/* @var $this yii\web\View */
/* @var $model common\models\VolOrganizzazione */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vol-organizzazione-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>

    <div class="row" ng-app="evento">

    	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            
        <?php 
            if($model->update_zona_allerta_strategy == \common\models\ZonaAllertaStrategy::getZonaManuale()) {
                $model->manual_zona_update = 'true';
            } else {
                $model->manual_zona_update = 'false';
            }

            echo $form->field($model, 'manual_zona_update')->checkbox(array(
                'label'=>'',
                'labelOptions'=>array('style'=>'padding:5px;'),
                'ng-init' => 'ctrl.manual_zona_update = '.$model->manual_zona_update,
                'ng-model' => 'ctrl.manual_zona_update'
            ))
            ->label('Abilita zona manuale di aggiornamento'); 

            $model->zone_allerta_array = explode(",", $model->zone_allerta);
            
            echo $form->field($model, 'zone_allerta_array', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                'data' => ArrayHelper::map( \common\models\AlmZonaAllerta::find()->all(), 'code', 'code'),
                'options' => [
                    'placeholder' => 'Zone di allerta...',
                    'ng-model' => 'ctrl.zone_allerta_array',
                    'ng-init' => 'ctrl.zone_allerta_array = ['.implode(",",array_map(function($e){ return '"'.$e.'"'; }, $model->zone_allerta_array)).']',
                    'ng-disabled' => '!(ctrl.manual_zona_update)'
                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => true
                ],
            ]);
                
        ?>
    	</div>

        
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crea' : 'Modifica', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
