<?php

use common\models\UtlEvento;
use common\models\UtlExtraSegnalazione;
use common\models\UtlExtraUtente;
use common\models\UtlRuoloSegnalatore;
use common\models\UtlSegnalazione;
use common\models\UtlTipologia;
use common\models\UtlUtente;
use common\models\LocComune;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UtlSegnalazione */
/* @var $form yii\widgets\ActiveForm */

$model = new UtlSegnalazione();
$utente = new UtlUtente();
$utente->scenario = 'createSegnalatore';
?>

<div class="utl-segnalazione-form" ng-app="segnalazione" ng-controller="segnalazioneFormController as ctrl">

    <?php $form = ActiveForm::begin([
        'options'=>['enctype'=>'multipart/form-data'], // important
        'action' => ['add-segnalazione?idEvento='.$evento->id]
    ]); ?>

    <?php echo $form->field($model, 'idutente')->hiddenInput(['value' => $utente->id])->label(false); ?>

    <div class="row">

        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-12">


            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                    <h5 class="m10h text-uppercase color-gray">Dati segnalatore</h5>
                    <?= $form->field($utente, 'nome',['options' => ['class'=>'col-lg-6 no-pl']])->textInput(); ?>
                    <?= $form->field($utente, 'cognome',['options' => ['class'=>'col-lg-6 no-pr']])->textInput(); ?>
                    <?= $form->field($utente, 'telefono',['options' => ['class'=>'col-lg-6 no-pl']])->textInput(); ?>
                    <?= $form->field($utente, 'email',['options' => ['class'=>'col-lg-6 no-pr']])->textInput(); ?>
                    <?= $form->field($utente, 'id_ruolo_segnalatore',['options' => ['class'=>'col-lg-12 no-pr no-pl', 'ng-show' => 'ctrl.tipoSegnalatore == 2']])->dropDownList(
                        ArrayHelper::map(UtlRuoloSegnalatore::find()->all(), 'id', 'descrizione'),
                        [
                            'prompt' => 'Seleziona un ruolo...',
                            'ng-model' => 'ctrl.ruoloSegnalatore',
                            'ng-init' => "ctrl.ruoloSegnalatore = '".$utente->id_ruolo_segnalatore."'"
                        ]
                    )->label('Ruolo del segnalatore (in caso di ente pubblico)'); ?>

                    <?= $form->field($utente, 'tipo',['options' => ['class'=>'col-lg-12 no-pr no-pl']])->dropDownList(
                        [1 => 'Cittadino Privato', 2 => 'Ente Pubblico'],
                        [
                            'ng-model' => 'ctrl.tipoSegnalatore',
                            'ng-change' => 'ctrl.id_tipo_ente_pubblico = null; ctrl.ruoloSegnalatore = null',
                            'ng-init' => "ctrl.tipoSegnalatore = '".$utente->tipo."'"
                        ]
                    )->label('Tipologia segnalatore'); ?>

                    <?php $idEp =  $utente->id_tipo_ente_pubblico; ?>
                    <div ng-show="ctrl.tipoSegnalatore == 2" ng-init="ctrl.id_tipo_ente_pubblico = '<?php echo $idEp; ?>'">
                        <?= $form->field($utente, 'id_tipo_ente_pubblico')->radioList(
                            ArrayHelper::map(UtlExtraUtente::find()->orderBy(
                                [
                                    'parent_id'=>SORT_DESC,
                                    'id' => SORT_ASC
                                ])->all(), 'id', 'voce'),
                            [
                                'item' => function ($index, $label, $name, $checked, $value){
                                    $checkbox = UtlExtraUtente::findOne($value);
                                    $checkedTag = $checked ? 'checked' : '';
                                    if(!empty($checkbox->parent_id)){
                                        return  "<div class='radio'>------ <label><input id='check-{$index}' ng-model='ctrl.id_tipo_ente_pubblico' type='radio' {$checkedTag} name='{$name}' value='{$value}'>{$label}</label></div>";
                                    }else{
                                        return  "<div class='radio'><label class=''><input id='check-{$index}' ng-model='ctrl.id_tipo_ente_pubblico' type='radio' {$checkedTag} name='{$name}' value='{$value}'>{$label}</label></div>";
                                    }
                                }
                            ]
                        )->label('Tipo ente pubblico'); ?>
                    </div>

                </div>
            </div>

            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                    <h5 class="m10h text-uppercase color-gray">Dati Segnalazione</h5>

                    <?php
                    $model->fonte = array_search($model->fonte, $model->getFonteArray());
                    echo $form->field($model, 'fonte', ['options' => ['class'=>'col-lg-12 no-pl no-pr']])->dropDownList(
                        $model->getFonteArray()
                    );
                    ?>

                    <?= $form->field($model, 'tipologia_evento', ['options' => ['class'=>'col-lg-12 no-pl no-pr']])->dropDownList(ArrayHelper::map(
                        UtlTipologia::find()->all(), 'id', 'tipologia'
                    )); ?>

                    <?php
                    echo $form->field($model, 'idcomune', ['options' => ['class'=>'col-lg-5 no-pl']])->widget(Select2::classname(), [
                        'data' => ArrayHelper::map( LocComune::find()->where(['id_regione' => 18])->all(), 'id', 'comune'),
                        'options' => [
                            'placeholder' => 'Seleziona un comune...',
                            'ng-model' => 'ctrl.comune',
                            'ng-disabled' => 'ctrl.luogo',
                            'ng-init' => "ctrl.comune = '".$model->idcomune."'"
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>

                    <?= $form->field($model, 'indirizzo',['options' => ['class'=>'col-lg-7 no-pr']])
                        ->textInput([
                            'ng-model'=> 'ctrl.indirizzo',
                            'ng-disabled' => 'ctrl.luogo',
                            'ng-init' => "ctrl.indirizzo = '".$model->indirizzo."'"
                        ]); ?>

                    <?= $form->field($model, 'luogo',['options' => ['class'=>'col-lg-12 no-pl no-pr']])
                        ->textInput([
                            'ng-model'=> 'ctrl.luogo',
                            'ng-disabled'=> 'ctrl.comune || ctrl.indirizzo',
                        ]); ?>

                    <?php if(!($model->isNewRecord) && ($model->fonte == 1)):?>

                        <?= $form->field($model, 'direzione')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'distanza')->textInput() ?>

                    <?php endif; ?>
                </div>
            </div>

        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-12">

            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                    <h5 class="m10h text-uppercase color-gray">Informazioni sulla segnalazione</h5>

                    

                    <?= $form->field($model, 'extras')->checkboxList(
                        
                        ArrayHelper::map(UtlExtraSegnalazione::find()->orderBy(
                            [
                                'parent_id'=>SORT_DESC,
                                'id'=>SORT_ASC
                            ])->all(), 'id', 'voce'),
                        [
                            
                            'item' => function ($index, $label, $name, $checked, $value){
                                $checkbox = UtlExtraSegnalazione::findOne($value);
                                $checkedTag = $checked ? 'checked' : '';
                                if(!empty($checkbox->parent_id)){
                                    return  "<div class='checkbox'>------ <label><input type='checkbox' {$checkedTag} name='{$name}' value='{$value}'>{$label}</label></div>";
                                }else{
                                    return  "<div class='checkbox'><label class=''><input type='checkbox' {$checkedTag} name='{$name}' value='{$value}'>{$label}</label></div>";
                                }
                            }
                        ]
                    )->label(false); ?>

                </div>
            </div>

            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                    <h5 class="m10h text-uppercase color-gray">Ulteriori informazioni sulla segnalazione</h5>

                    <?= $form->field($model, 'note')->textarea(['rows' => 4]) ?>

                </div>
            </div>

        </div>

    </div>

    <div class="form-group p5w">
        <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Crea' : 'Aggiorna', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
