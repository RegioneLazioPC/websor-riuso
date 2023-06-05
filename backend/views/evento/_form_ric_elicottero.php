<?php

use yii\bootstrap\ActiveForm;
use common\models\UtlAutomezzo;
use common\models\UtlFunzioniSupporto;
use common\models\UtlSquadraOperativa;
use common\models\UtlTask;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use common\models\LocComune;
use kartik\widgets\DateTimePicker;


$elicotteri = UtlAutomezzo::find()->joinWith(['tipo'])->where(['UPPER(utl_automezzo_tipo.descrizione)' => 'ELICOTTERO'])
->orderBy(['targa'=>SORT_ASC])->asArray()->all();
/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */
/* @var $form yii\widgets\ActiveForm */
if(!$model->engaged) $model->engaged = 0;
$actioForm = !isset($model->id) ? 'evento/create-elicottero' : 'evento/update-elicottero?id='.$model->id;


$curr = new \DateTime();
$curr->setTimezone(new \DateTimezone('Europe/Rome'));

$form = ActiveForm::begin([
    'action' =>[$actioForm],
    'id' => 'newElicottero'
]);
?>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

        <?php if(!empty($model->id)): ?>
            <div class="row m5w m20h bg-grayLighter box_shadow">
                <?php echo common\widgets\Alert::widget();?>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                <?php if(Yii::$app->user->can('updateRichiestaElicottero')) { ?>
                    <h5 class="m10h text-uppercase color-gray"> Approvazione richiesta</h5>

                    <div class="col-md-12 no-pl">
                        
                        <?php

                        /**
                         * Mostriamo approvazione solo se non già data
                         */
                        if($model->edited == 0) {
                            echo $form->field($model, 'engaged')->radioList(
                            [
                                0=>'No', 
                                true=> 'Si'
                            ])->label('Approvazione'); 
                        } else {
                            /**
                             * Altrimenti diciamo lo stato
                             */
                            echo ($model->engaged) ? "Richiesta approvata" : "Richiesta rifiutata";
                        }
                        ?>
                        
                    </div>
                    <?php } ?>
                    <div class="col-sm-6 no-pl">
                        <h5>&nbsp;</h5>
                <?php 

                    if(!empty($model->codice_elicottero)) {
                        
                        echo $form->field($model, 'codice_elicottero',['options'=>['class'=>'col-lg-4 no-pl']])->textInput()->label('Codice Elicottero'); 
                    
                    } else {
                        
                        echo $form->field($model, 'id_elicottero', ['options'=>['class'=>'col-lg-8 no-pl']])->dropDownList(
                            ArrayHelper::map( $elicotteri, 'id', 'targa'), 
                            [
                                'options' => [
                                    'SOPPRESSIONE'=>['selected'=>true]
                                ],
                                'prompt' => 'Seleziona elicottero...'
                            ]
                        )
                        ->label('Codice elicottero');
                            
                    }
                ?>
                    </div>
                    <div class="col-sm-6 no-pl">
                        <h5>Data e ora decollo</h5>
                        <?php 
                        if(!empty($model->dataora_decollo)){
                            $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $model->dataora_decollo);
                            if(is_bool($dt)) $dt = new \DateTime();
                            
                            $model->dataora_decollo = $dt->format('d-m-Y H:i');
                            $model->date = $dt->format('d-m-Y');
                            $model->hour = $dt->format('H');
                            $model->minutes = $dt->format('i');
                        }
                        ?>
                        <div class="col-sm-6 no-pl">
                        <?php
                        if(empty($model->date)) $model->date = $curr->format('d-m-Y');
                        echo $form->field($model, 'date', [])->textInput(); 
                        ?>
                        </div>
                        <div class="col-sm-3 no-pl">
                        <?php
                        echo $form->field($model, 'hour', [])->textInput(['type'=>'number','min'=>0,'max'=>24]); 
                        ?>
                        </div>
                        <div class="col-sm-3 no-pl">
                        <?php
                        echo $form->field($model, 'minutes', [])->textInput(['type'=>'number','min'=>0,'max'=>59]); 
                        ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                    <div class="col-sm-6 no-pl">
                        <h5>Data e ora arrivo stimato</h5>
                        <?php 
                        if(!empty($model->dataora_arrivo_stimato)){
                            $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $model->dataora_arrivo_stimato);
                            $model->dataora_arrivo_stimato = $dt->format('d-m-Y H:i');
                            $model->date_arrivo_stimato = $dt->format('d-m-Y');
                            $model->hour_arrivo_stimato = $dt->format('H');
                            $model->minutes_arrivo_stimato = $dt->format('i');
                        }
                        ?>
                        <div class="col-sm-6 no-pl">
                        <?php
                        if(empty($model->date_arrivo_stimato)) $model->date_arrivo_stimato = $curr->format('d-m-Y');
                        echo $form->field($model, 'date_arrivo_stimato', [])->label('Data')->textInput(); 
                        ?>
                        </div>
                        <div class="col-sm-3 no-pl">
                        <?php
                        echo $form->field($model, 'hour_arrivo_stimato', [])->label('ore')->textInput(['type'=>'number','min'=>0,'max'=>24]); 
                        ?>
                        </div>
                        <div class="col-sm-3 no-pl">
                        <?php
                        echo $form->field($model, 'minutes_arrivo_stimato', [])->label('minuti')->textInput(['type'=>'number','min'=>0,'max'=>59]); 
                        ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                    <div class="col-sm-6 no-pl">
                        <h5>Numero di lanci</h5>
                        
                        <?php
                        echo $form->field($model, 'n_lanci', [])->label('Numero')->textInput(['type'=>'number']); 
                        ?>
                        
                    </div>
                    <div class="col-sm-6 no-pl">
                        <h5>Data e ora atterraggio</h5>
                        <?php 
                        if(!empty($model->dataora_atterraggio)){
                            $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $model->dataora_atterraggio);

                            $model->dataora_atterraggio = $dt->format('d-m-Y H:i');
                            $model->date_atterraggio = $dt->format('d-m-Y');
                            $model->hour_atterraggio = $dt->format('H');
                            $model->minutes_atterraggio = $dt->format('i');
                        }
                        ?>
                        <div class="col-sm-6 no-pl">
                        <?php
                        echo $form->field($model, 'date_atterraggio', [])->label('Data')->textInput(); 
                        ?>
                        </div>
                        <div class="col-sm-3 no-pl">
                        <?php
                        echo $form->field($model, 'hour_atterraggio', [])->label('ore')->textInput(['type'=>'number','min'=>0,'max'=>24]); 
                        ?>
                        </div>
                        <div class="col-sm-3 no-pl">
                        <?php
                        echo $form->field($model, 'minutes_atterraggio', [])->label('minuti')->textInput(['type'=>'number','min'=>0,'max'=>59]); 
                        ?>
                        </div>

                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row m5w m20h bg-grayLighter box_shadow">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">

                <h5 class="m10h text-uppercase color-gray"> Località</h5>

                <div class="row">
                    <div class="col-sm-6">
                        <?php 

                        if(empty($model->id_comune) && !empty($evento->idcomune)) $model->id_comune = $evento->idcomune;

                        echo $form->field($model, 'id_comune', ['options'=>['class'=>'col-lg-8 no-pl']])->dropDownList(
                            ArrayHelper::map( array_merge( ['id'=>null, 'comune' => ''], LocComune::find()->where(
                                [
                                    Yii::$app->params['region_filter_operator'], 
                                    'id_regione', 
                                    Yii::$app->params['region_filter_id']
                                ])->orderBy(['comune'=>SORT_ASC])->all()), 'id', 'comune'), 
                            ['options' => ['SOPPRESSIONE'=>['selected'=>true]]])->label('Comune');
                        
                    ?>
                    </div>
                    <div class="col-sm-6">
                        <div ng-init="init('<?php echo Yii::$app->request->csrfToken;?>')">
                            <?php 
                            if(empty($model->localita)) {
                                if( !empty($evento->indirizzo) ) $model->localita = $evento->indirizzo;
                                if( !empty($evento->luogo) ) $model->localita = $evento->luogo;
                            }
                            ?>
                            <?php echo $form->field($model, 'localita', [])->textInput() ?>                            
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row m5w m20h bg-grayLighter box_shadow">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">

                <h5 class="m10h text-uppercase color-gray"> Tipo Intervento e vegetazione</h5>

                <?php

                echo $form->field($model, 'missione', ['options'=>['class'=>'col-lg-4 no-p']])->dropDownList([ 
                        'SOPPRESSIONE' => 'SOPPRESSIONE', 
                        'RICOGNIZIONE' => 'RICOGNIZIONE', 
                        'BONIFICA' => 'BONIFICA',
                        'ALTRO' => 'ALTRO',  
                    ], ['options' => ['SOPPRESSIONE'=>['selected'=>true]]]);
                
                ?>

                
                <?= $form->field($model, 'area_bruciata', ['options'=>['class'=>'col-lg-4']])->textInput() ?>

                <?= $form->field($model, 'area_rischio',  ['options'=>['class'=>'col-lg-4']])->textInput() ?>

                
            </div>
        </div>

        <div class="row m5w m20h bg-grayLighter box_shadow">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">

                <h5 class="m10h text-uppercase color-gray">Dettagli incendio</h5>

                <?= $form->field($model, 'fronte_fuoco_num',  ['options'=>['class'=>'col-lg-6 no-p']])->textInput() ?>

                <?= $form->field($model, 'fronte_fuoco_tot',  ['options'=>['class'=>'col-lg-6']])->textInput() ?>

                <?= $form->field($model, 'elettrodotto', ['options'=>['class'=>'col-lg-6 no-p']])->dropDownList([ 'Non definito' => 'Non definito', 'Nessuno' => 'Nessuno', 'Da disattivare' => 'Da disattivare', 'A distanza di sicurezza' => 'A distanza di sicurezza', ], ['options' => ['Non definito'=>['selected'=>true]]]) ?>

                <?= $form->field($model, 'oreografia', ['options'=>['class'=>'col-lg-6']])->dropDownList([ 'Non definito' => 'Non definito', 'Pianura' => 'Pianura', 'Collina' => 'Collina', 'Montagna' => 'Montagna', 'Impervia' => 'Impervia', ], ['options' => ['Non definito'=>['selected'=>true]]]) ?>

                <?= $form->field($model, 'vento',  ['options'=>['class'=>'col-lg-6 no-p']])->dropDownList([ 'Non definito' => 'Non definito', 'Nessuno' => 'Nessuno', 'Debole' => 'Debole', 'Moderato' => 'Moderato', 'Forte' => 'Forte', ], ['options' => ['Non definito'=>['selected'=>true]]]) ?>

                <?= $form->field($model, 'ostacoli',  ['options'=>['class'=>'col-lg-6']])->dropDownList([ 'Non definito' => 'Non definito', 'Nessuno' => 'Nessuno', 'Infrastrutture' => 'Infrastrutture', 'Abitazioni' => 'Abitazioni', 'Fili a sbalzo - Teleferiche' => 'Fili a sbalzo - Teleferiche', ], ['options' => ['Non definito'=>['selected'=>true]]]) ?>

                <?= $form->field($model, 'note')->textarea(['rows' => 6])->label("Note") ?>

            </div>
        </div>

        <?php echo $form->field($evento, 'id')->hiddenInput()->label(false); ?>
        <?php echo $form->field($model, 'id')->hiddenInput()->label(false); ?>

    </div>

</div>

<div class="form-group">
    <?php 
    if($model->deleted != 1) {
        if(Yii::$app->user->can('updateRichiestaElicottero')) {
            echo Html::submitButton('<i class="fa fa-save p5w"></i> Salva e invia mail', ['class' => 'btn btn-success']);
        } else {
            echo Html::submitButton('<i class="fa fa-save p5w"></i> Aggiorna', ['class' => 'btn btn-success']);
        }
    }
    ?>

</div>

<?php ActiveForm::end(); ?>

