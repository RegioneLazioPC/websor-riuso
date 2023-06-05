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
            if(empty($model->id_sync)) {

                echo $form->field($model, 'id_tipo_organizzazione', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                    'data' => ArrayHelper::map( VolTipoOrganizzazione::find()->orderBy(['tipologia'=>SORT_ASC])->all(), 'id', 'tipologia'),
                    'options' => [
                        'placeholder' => 'Tipo organizzazione...',
                        'ng-model' => 'ctrl.tipo_organizzazione',
                        'ng-disabled' => 'ctrl.luogo',
                        'ng-init' => "ctrl.tipo_organizzazione = '".$model->id_tipo_organizzazione."'"
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);

                 
                echo $form->field($model, 'stato_iscrizione', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                    'data' => [3=>'Accreditata',4=>'Sospesa'],
                    'options' => [
                        'placeholder' => 'Stato iscrizione...',
                        'ng-model' => 'ctrl.stato_iscrizione',
                        'ng-disabled' => 'ctrl.st',
                        'ng-init' => "ctrl.stato_iscrizione = '".$model->stato_iscrizione."'"
                    ],
                    'pluginOptions' => [
                        'allowClear' => false
                    ],
                ]);

                

                echo $form->field($model, 'ref_id')->textInput();

                echo $form->field($model, 'denominazione')->textInput();

                echo $form->field($model, 'codicefiscale')->textInput();

                echo $form->field($model, 'partita_iva')->textInput();

                echo $form->field($model, 'tipo_albo_regionale')->dropDownList([ 'D.D.G.' => 'D.D.G.', 'D.D.S.' => 'D.D.S.', 'D.G.R.' => 'D.G.R.', ],  ['options' => ['D.D.G'=>['selected'=>true]]]);

                echo $form->field($model, 'num_albo_regionale')->textInput();

                
                echo $form->field($model, 'nome_referente')->textInput();
                echo $form->field($model, 'tel_referente')->textInput();

            }
            echo $form->field($model, 'num_comunale')->textInput();

            
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
                    'ng-init' => 'ctrl.zone_allerta_array = ['.implode(",",array_map(function($e){ return '"'.Html::encode($e).'"'; }, $model->zone_allerta_array)).']',
                    'ng-disabled' => '!(ctrl.manual_zona_update)'
                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => true
                ],
            ]);
            
                
        ?>
    	</div>

        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?php

            if(empty($model->id_sync)) {

                echo $form->field($model, 'num_albo_provinciale')->textInput();

                echo $form->field($model, 'num_albo_nazionale')->textInput();

                echo $form->field($model, 'num_assicurazione')->textInput();

                echo $form->field($model, 'societa_assicurazione')->textInput();

            
                $model->data_scadenza_assicurazione = ($model->data_scadenza_assicurazione) ? Yii::$app->formatter->asDate($model->data_scadenza_assicurazione) : $now->format('d-m-Y'); 
                echo $form->field($model, 'data_scadenza_assicurazione', ['options' => ['class' => '']])->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => 'Data scadenza assicurazione ...'],
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'language' => 'it',
                        'format' => 'dd-mm-yyyy'
                    ],
                ]); 
            

            
                $model->data_albo_regionale = ($model->data_albo_regionale) ? Yii::$app->formatter->asDate($model->data_albo_regionale) : $now->format('d-m-Y'); 
            
                echo $form->field($model, 'data_albo_regionale', ['options' => ['class' => '']])->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => 'Data albo regionale ...'],
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'language' => 'it',
                        'format' => 'dd-mm-yyyy'
                    ],
                ]); 
            

            

                echo $form->field($model, 'nome_responsabile')->textInput();
                echo $form->field($model, 'tel_responsabile')->textInput();

            }
            ?>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><?php echo $form->field($model, 'note')->textInput() ?></div>
        
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crea' : 'Modifica', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
