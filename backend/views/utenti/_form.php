<?php

use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

use common\models\UtlAnagrafica;
use common\models\LocComune;

use common\models\ViewRubrica;
use common\models\MasRubrica;
use common\models\UtlRuoloSegnalatore;

$comuni = LocComune::find()->where([
                                Yii::$app->params['region_filter_operator'], 
                                'id_regione', 
                                Yii::$app->params['region_filter_id']
                            ])->orderBy(['comune'=>SORT_ASC])->all();


$anagrafica = ($model->anagrafica) ? $model->anagrafica : new UtlAnagrafica();
if(Yii::$app->request->post('UtlAnagrafica')) $anagrafica->load(Yii::$app->request->post());

$anagrafica->scenario = UtlAnagrafica::SCENARIO_UTL_UTENTE;

$rubrica = ($model->rubrica) ? $model->rubrica : new MasRubrica();
if(Yii::$app->request->post('MasRubrica')) $rubrica->load(Yii::$app->request->post());
?>

<div class="utl-utente-form">
    <?php 
    if(isset($errors)){
        var_dump($errors);
    }
    ?>
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'validateOnChange'=> true,
    ]); ?>

    
    <div class="row">
    	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
            <?= $form->field($anagrafica, 'nome', ['options' => ['class' => 'col-md-6 no-pl']])->textInput(['maxlength' => true]) ?>

            <?= $form->field($anagrafica, 'cognome', ['options' => ['class' => 'col-md-6 no-pl']])->textInput(['maxlength' => true]) ?>

            <?= $form->field($anagrafica, 'codfiscale', ['options' => ['class' => 'col-md-6 no-pl']])->textInput(['maxlength' => true]) ?>

            <?= 
                $form->field($anagrafica, 'comune_residenza', ['options' => ['class'=>'col-md-6 no-pl']])->widget(Select2::classname(), [
                    'data' => ArrayHelper::map( $comuni, 'id', 'comune'),
                    'attribute' => 'org_id',
                    'options' => [
                        'multiple' => false,
                        'theme' => 'krajee',
                        'placeholder' => 'Cerca comune',
                        'language' => 'it-IT',
                        'width' => '100%',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);

            ?>

            
            <?php 
            $anagrafica->data_nascita = ($anagrafica->data_nascita) ? Yii::$app->formatter->asDate($anagrafica->data_nascita) : "";
            ?>
            <?php echo $form->field($anagrafica, 'data_nascita', ['options' => ['class' => 'col-md-6 no-pl']])->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => 'Data di nascita ...'],
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'language' => 'it',
                        'format' => 'dd-mm-yyyy'
                    ],
                ]); ?>

            <?= 
                $form->field($anagrafica, 'luogo_nascita', ['options' => ['class'=>'col-md-6 no-pl']])->widget(Select2::classname(), [
                    'data' => ArrayHelper::map( LocComune::find()->orderBy(['comune'=>SORT_ASC])->all(), 'comune', 'comune'),
                    'attribute' => 'org_id',
                    'options' => [
                        'multiple' => false,
                        'theme' => 'krajee',
                        'placeholder' => 'Cerca comune',
                        'language' => 'it-IT',
                        'width' => '100%',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);

            ?>

    	</div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">

            <?= $form->field($anagrafica, 'telefono', ['options' => ['class' => 'col-md-6 no-pl']])->textInput(['maxlength' => true, 'value' => @$anagrafica->telefono]) ?>

            <?= $form->field($anagrafica, 'email', ['options' => ['class' => 'col-md-6 no-pl']])->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'id_ruolo_segnalatore', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(UtlRuoloSegnalatore::find()->asArray()->all(), 'id', 'descrizione'),
                    'options' => [
                        'placeholder' => 'Seleziona il tipo di segnalatore...',
                        'ng-model' => 'ctrl.segnalatore_type',
                        'ng-init' => "ctrl.segnalatore_type = '".$model->id_ruolo_segnalatore."'"
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label('Ruolo segnalatore');
                ?>

            <?= $form->field($rubrica, 'ruolo', ['options' => ['class'=>'']])->widget(Select2::classname(), [
                    'data' => ViewRubrica::getTipiRiferimento(),
                    'options' => [
                        'placeholder' => 'Seleziona il tipo di contatto...',
                        'ng-model' => 'ctrl.contatto_type',
                        'ng-init' => "ctrl.contatto_type = '".$rubrica->ruolo."'"
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label('Tipo contatto');
                ?>
            
        </div>
    </div>

    <div class="form-group m20h">
        <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Registra utente' : 'Aggiorna', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
