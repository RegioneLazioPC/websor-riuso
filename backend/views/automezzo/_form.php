<?php

use yii\helpers\Html;

use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

use common\models\UtlCategoriaAutomezzoAttrezzatura;
use common\models\UtlAutomezzoTipo;
use common\models\VolOrganizzazione;
use common\models\VolSede;
use kartik\widgets\DatePicker;

$select2Options = [
    'multiple' => false,
    'theme' => 'krajee',
    'placeholder' => 'Inizia a digitare',
    'language' => 'it-IT',
    'width' => '100%',
];

/* @var $this yii\web\View */
/* @var $model common\models\UtlAutomezzo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-automezzo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'targa')->textInput(['maxlength' => true]) ?>

    <?php  
        if($model->data_immatricolazione) :
            $data_immatricolazione = \DateTime::createFromFormat('Y-m-d', $model->data_immatricolazione);
            $model->data_immatricolazione = $data_immatricolazione->format('d-m-Y');
        endif;
        echo $form->field($model, 'data_immatricolazione', ['options' => ['class' => '']])->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Data immatricolazione ...'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'language' => 'it',
                    'format' => 'dd-mm-yyyy'
                ],
            ]); 
        ?>

    <?= $form->field($model, 'classe')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sottoclasse')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'modello')->textInput(['maxlength' => true]) ?>


    <?= 
        $form->field($model, 'idtipo', ['options' => ['class'=>'']])->widget(Select2::classname(), [
            'data' => ArrayHelper::map( UtlAutomezzoTipo::find()->all(), 'id', 'descrizione'),
            'options' => [
                'placeholder' => 'Tipo...',
                'ng-model' => 'ctrl.tipo',
                'ng-disabled' => 'ctrl.tp',
                'ng-init' => "ctrl.tipo = '".$model->idtipo."'"
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    ?>

    <?= $form->field($model, 'capacita')->textInput() ?>

    <?= $form->field($model, 'disponibilita')->textInput(['maxlength' => true]) ?>

    <?= 
        $form->field($model, 'idorganizzazione', ['options' => ['class'=>'']])->widget(Select2::classname(), [
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
        $form->field($model, 'idsede', ['options' => ['class'=>'']])->widget(Select2::classname(), [
            'data' => ArrayHelper::map( VolSede::find()
                ->andWhere(['id_organizzazione'=>$model->idorganizzazione])
                ->all(), 'id', 'indirizzo'),
            'attribute' => 'idsede',
            'options' => $select2Options,
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php ob_start(); // output buffer the javascript to register later ?>
<script>
    function populateOrgSedi(org_id) {
        var url = '<?= Url::to(['automezzo/populate-org-sedi', 'id' => '-id-']) ?>';
        var $select = $('#utlautomezzo-idsede');
        $select.find('option').remove().end();
        $.ajax({
            url: url.replace('-id-', org_id),
            success: function (data) {
                console.log(data, $select)
                var select2Options = <?= Json::encode($select2Options) ?>;
                select2Options.data = data.data;
                $select.select2(select2Options);
                //$select.val(data.selected).trigger('change');
            }
        });
    }
</script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean()), View::POS_END); ?>
