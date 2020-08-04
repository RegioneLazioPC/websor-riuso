<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;



use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

use common\models\VolOrganizzazione;
use common\models\VolSede;
use kartik\widgets\DatePicker;

use common\models\UtlAnagrafica;

$anagrafica = ($model->anagrafica) ? $model->anagrafica : new UtlAnagrafica();
$anagrafica->scenario = UtlAnagrafica::SCENARIO_VOLONTARIO;

/* @var $this yii\web\View */
/* @var $model common\models\VolVolontario */
/* @var $form yii\widgets\ActiveForm */
$select2Options = [
    'multiple' => false,
    'theme' => 'krajee',
    'placeholder' => 'Inizia a digitare',
    'language' => 'it-IT',
    'width' => '100%',
];
?>

<div class="vol-volontario-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($anagrafica, 'nome')->textInput() ?>
    <?= $form->field($anagrafica, 'cognome')->textInput() ?>

    <?= $form->field($anagrafica, 'codfiscale')->textInput() ?>

    <?= $form->field($model, 'ruolo')->dropDownList([ 'Presidente' => 'Presidente', 'Vice Presidente' => 'Vice Presidente', 'Rappresentante Legale' => 'Rappresentante Legale', 'Volontario' => 'Volontario', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'spec_principale')->textInput(['maxlength' => true]) ?>


    <?= 
        $form->field($model, 'valido_dal', ['options' => ['class' => '']])->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Valido dal ...'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'language' => 'it',
                    'format' => 'dd-mm-yyyy'
                ],
            ]); 
        ?>
    <?= 
        $form->field($model, 'valido_al', ['options' => ['class' => '']])->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Valido al ...'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'language' => 'it',
                    'format' => 'dd-mm-yyyy'
                ],
            ]); 
        ?>

    <?= $form->field($model, 'operativo')->checkbox() ?>

    <?= 
        $form->field($model, 'id_organizzazione', ['options' => ['class'=>'']])->widget(Select2::classname(), [
            'data' => ArrayHelper::map( VolOrganizzazione::find()->all(), 'id', 'denominazione'),
            'attribute' => 'org_id',
            'options' => $select2Options,
            'pluginOptions' => [
                'allowClear' => true
            ],
            'pluginEvents' => [
                'select2:select' => 'function(e) { populateOrgSedi(e.params.data.id); }',
            ],
        ]);

    ?>

    <?= 
        $form->field($model, 'id_sede', ['options' => ['class'=>'']])->widget(Select2::classname(), [
            'data' => ArrayHelper::map( VolSede::find()
                ->andWhere(['id_organizzazione'=>$model->id_organizzazione])
                ->all(), 'id', function($model) {
                return $model['indirizzo'].' - '.$model['tipo'];
            }),
            'attribute' => 'id_sede',
            'options' => $select2Options,
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    ?>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php ob_start(); // output buffer the javascript to register later ?>
<script>
    function populateOrgSedi(org_id) {
        var url = '<?= Url::to(['volontari/populate-org-sedi', 'id' => '-id-']) ?>';
        
        var $select = $('#volvolontario-id_sede');
        $select.find('option').remove().end();
        $.ajax({
            url: url.replace('-id-', org_id),
            success: function (data) {
                console.log(data, $select)
                var select2Options = <?= Json::encode($select2Options) ?>;
                select2Options.data = data.data;
                $select.select2(select2Options);
            }
        });
    }
</script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean()), View::POS_END); ?>