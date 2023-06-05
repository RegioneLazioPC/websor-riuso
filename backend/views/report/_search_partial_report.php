<?php

use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\models\reportistica\FilterModel;
use common\models\LocProvincia;
use common\models\LocComune;
use common\models\UtlTipologia;
use common\models\UtlAutomezzoTipo;
use common\models\UtlIngaggio;
use common\models\VolOrganizzazione;

use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEventoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reportistica-search">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"></h3>
            <h2 class="panel-title">Filtra i risultati</h2>
            <div class="clearfix"></div>
        </div>


        <div class="panel-body">
            <?php
            $form = ActiveForm::begin([
                'action' => [''],
                'method' => 'get',
            ]); ?>

            <?php

            if (isset($year)) {
                $years = [];
                $anno = date('Y');

                for ($n = 0; $n < 10; $n++) {
                    $y = $anno - $n;
                    $years[$y] = $y;
                }


                echo $form->field($filter_model, 'year', ['options' => ['class' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12']])->widget(Select2::classname(), [
                    'data' => $years,
                    'options' => [
                        'placeholder' => 'Anno...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }
            ?>


            <?php

            if (isset($month)) {
                $month = [];
                for ($n = 1; $n <= 12; $n++) {
                    $month[$n] = FilterModel::$months[$n];
                }


                echo $form->field($filter_model, 'month', ['options' => ['class' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12']])->widget(Select2::classname(), [
                    'data' => $month,
                    'options' => [
                        'placeholder' => 'Mese...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }
            ?>

            <div class="clearfix"></div>

            <?php
            if (!empty($pr)) {
                echo $form->field($filter_model, 'pr', ['options' => ['class' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12']])->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(LocProvincia::find()->where(
                        [
                            Yii::$app->params['region_filter_operator'],
                            'id_regione',
                            Yii::$app->params['region_filter_id']
                        ]
                    )->orderBy(['sigla' => SORT_ASC])->all(), 'sigla', 'sigla'),
                    'options' => [
                        'sigla' => 'pr',
                        'placeholder' => 'Seleziona la provincia...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }
            ?>

            <?php
            if (!empty($comune)) {
                echo $form->field($filter_model, 'comune', ['options' => ['class' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12']])->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(LocComune::find()->where(
                        [
                            Yii::$app->params['region_filter_operator'],
                            'id_regione',
                            Yii::$app->params['region_filter_id']
                        ]
                    )->orderBy(['comune' => SORT_ASC])->all(), 'id', 'comune'),
                    'options' => [
                        'placeholder' => 'Seleziona un comune...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }

            if (isset($stato_ingaggio)) {
                echo $form->field($filter_model, 'stato_ingaggio', ['options' => ['class' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12']])->widget(Select2::classname(), [
                    'data' => UtlIngaggio::getStati(),
                    'options' => [
                        'placeholder' => 'Stato...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }
            ?>

            <div class="clearfix"></div>



            <?php
            if (isset($tipologia)) {
                echo $form->field($filter_model, 'tipologia', ['options' => ['class' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12']])->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(UtlTipologia::find()->where('idparent is null')->orderBy(['tipologia' => SORT_ASC])->all(), 'id', 'tipologia'),
                    'options' => [
                        'placeholder' => 'Tipologia evento...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }

            if (isset($sottotipologia)) {
                echo $form->field($filter_model, 'sottotipologia', ['options' => ['class' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12']])->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(UtlTipologia::find()->where('idparent is not null')
                        ->orderBy(['idparent' => SORT_ASC])->all(), 'id', function ($model) {
                        return $model->tipologia . " (" . $model->tipologiaGenitore->tipologia . ")";
                    }),
                    'options' => [
                        'placeholder' => 'Sottotipologia evento...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }

            if (isset($tipo_mezzo)) {
                echo $form->field($filter_model, 'tipo_mezzo', ['options' => ['class' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12']])->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(UtlAutomezzoTipo::find()
                        ->orderBy(['descrizione' => SORT_ASC])->all(), 'id', 'descrizione'),
                    'options' => [
                        'placeholder' => 'Tipo di mezzo...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }
            ?>

            <div class="clearfix"></div>

            <?php

            if (isset($odv)) {
                echo $form->field($filter_model, 'odv', ['options' => ['class' => 'col-lg-4 col-md-4 col-sm-6 col-xs-12']])->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(VolOrganizzazione::find()->where('ref_id is not null')
                        ->orderBy(['ref_id' => SORT_ASC])->all(), 'ref_id', function ($element) {
                        return $element['ref_id'] . " - " . $element['denominazione'];
                    }),
                    'options' => [
                        'placeholder' => 'Organizzazione evento...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
            }

            ?>
            <div class="clearfix"></div>

            <?php
            if (isset($from)) {
                echo $form->field($filter_model, 'date_from', ['options' => ['class' => 'col-lg-6']])->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => 'Data dal'],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'autoclose' => true
                    ]
                ]);
            }
            ?>
            <?php
            if (isset($plain_date)) {
                echo $form->field($filter_model, 'date_from', ['options' => ['class' => 'col-lg-6']])->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => 'Data'],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'autoclose' => true
                    ]
                ])->label('Data');
            }
            ?>

            <?php
            if (isset($to)) {
                echo $form->field($filter_model, 'date_to', ['options' => ['class' => 'col-lg-6']])->widget(DatePicker::classname(), [
                    'options' => ['placeholder' => 'Data al'],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'autoclose' => true
                    ]
                ]);
            }
            ?>

            <?php
            if (isset($dataora)) {
                echo $form->field($filter_model, 'dataora', ['options' => ['class' => 'col-lg-6']])->widget(DateTimePicker::classname(), [
                    'options' => ['placeholder' => 'Data e ora'],
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd hh:ii',
                        'todayHighlight' => true,
                        'autoclose' => true
                    ]
                ]);
            }
            ?>

            <div class="form-group col-lg-12">
                <?= Html::submitButton('Cerca', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>

    </div>
    <div class="clearfix"></div>

</div>