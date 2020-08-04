<?php

use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

use common\models\UtlCategoriaAutomezzoAttrezzatura;
use common\models\UtlAttrezzaturaTipo;
use common\models\VolOrganizzazione;
use common\models\VolSede;
use common\models\UtlAutomezzo;

$select2Options = [
    'multiple' => false,
    'theme' => 'krajee',
    'placeholder' => 'Inizia a digitare',
    'language' => 'it-IT',
    'width' => '100%',
];
/* @var $this yii\web\View */
/* @var $model common\models\UtlAttrezzatura */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="utl-attrezzatura-form">

    <?php $form = ActiveForm::begin(); ?>

    
    <?= 
        $form->field($model, 'idtipo', ['options' => ['class'=>'']])->widget(Select2::classname(), [
            'data' => ArrayHelper::map( UtlAttrezzaturaTipo::find()->all(), 'id', 'descrizione'),
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

    <?= $form->field($model, 'classe')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sottoclasse')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'modello')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'capacita')->textInput() ?>

    <?= $form->field($model, 'unita')->textInput(['maxlength' => true]) ?>

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
            'pluginEvents' => [
                'select2:select' => 'function(e) { populateOrgSedeAutomezzi(e.params.data.id); }',
            ],
        ]);

    ?>

    <?= 
        $form->field($model, 'idautomezzo', ['options' => ['class'=>'']])->widget(Select2::classname(), [
            'data' => ArrayHelper::map( UtlAutomezzo::find()
                ->andWhere(['idorganizzazione'=>$model->idorganizzazione])
                ->andWhere(['idsede'=>$model->idsede])
                ->all(), 'id', 'targa'),
            'attribute' => 'idautomezzo',
            'options' => $select2Options,
            'pluginOptions' => [
                'allowClear' => true
            ],
            'pluginEvents' => [
                //'select2:select' => 'function(e) { populateOrgSedeAutomezzi(); }',
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
        var url = '<?= Url::to(['attrezzatura/populate-org-sedi', 'id' => '-id-']) ?>';
        var $select = $('#utlattrezzatura-idsede');
        $select.find('option').remove().end();
        populateOrgSedeAutomezzi();
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

    function populateOrgSedeAutomezzi() {

        var select_org = $('#utlattrezzatura-idorganizzazione').val() || '';
        var select_sed = $('#utlattrezzatura-idsede').val() || '';

        var url = '<?= Url::to(['attrezzatura/populate-org-sede-automezzi']) ?>?org='+select_org+'&sed='+select_sed;

        console.log(url);
        var $select = $('#utlattrezzatura-idautomezzo');
        $select.find('option').remove().end();
        $.ajax({
            url: url,
            success: function (data) {
                console.log(data)
                var select2Options = <?= Json::encode($select2Options) ?>;
                select2Options.data = data.data;
                $select.select2(select2Options);
                //$select.val(data.selected).trigger('change');
            }
        });
        
    }
</script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean()), View::POS_END); ?>







