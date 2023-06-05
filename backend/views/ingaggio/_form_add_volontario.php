<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use common\models\VolVolontario;

use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

use yii\widgets\Pjax;
use common\models\ConVolontarioIngaggio;
/* @var $this yii\web\View */
/* @var $model common\models\ConVolontarioIngaggio */
/* @var $form yii\widgets\ActiveForm */
$select2Options = [
    'multiple' => false,
    'theme' => 'krajee',
    'placeholder' => 'Seleziona un volontario',
    'language' => 'it-IT',
    'width' => '100%',
];


$volontari = VolVolontario::find()
->joinWith('anagrafica', ['utl_anagrafica.id' => 'vol_volontario.id_anagrafica'])
->where(['id_organizzazione'=>$organizzazione])
->andWhere(['operativo'=>true])
->orderBy(['utl_anagrafica.cognome'=>SORT_ASC, 'utl_anagrafica.nome' => SORT_ASC]);


$added = ConVolontarioIngaggio::find()
	->where(['id_ingaggio'=>$model->id_ingaggio])
	->select('id_volontario');

$volontari->andWhere(['not in', 'vol_volontario.id', $added]);

?>

<div class="con-volontario-ingaggio-form">

    <?php 
    
    $form = ActiveForm::begin(
    	[
    		'action' => 'add-volontario?id_ingaggio='.$model->id_ingaggio,
    		'id'=>'addformvolontarioingaggio'
    	]
    ); 
    ?>

    
    <?= 
        $form->field($model, 'id_volontario', ['options' => ['class'=>'']])->widget(Select2::classname(), [
            'data' => ArrayHelper::map( $volontari->all(), 'id', function($model) {
                return ($model['anagrafica']) ? $model['anagrafica']['cognome'].' '.$model['anagrafica']['nome'] : $model['id'];
            }),
            'attribute' => 'nome',
            'options' => $select2Options,
            'pluginOptions' => [
                'allowClear' => false
            ],
            'pluginEvents' => [
                'select2:select' => 'function(e) { 
                	
                	$(".field-convolontarioingaggio-nome").hide();
                	$(".field-convolontarioingaggio-cognome").hide();
                	$(".field-convolontarioingaggio-telefono").hide();
                	$(".field-convolontarioingaggio-email").hide();
                }',
                'select2:unselect' => 'function(e) { 
                	
                	$(".field-convolontarioingaggio-nome").show();
                	$(".field-convolontarioingaggio-cognome").show();
                	$(".field-convolontarioingaggio-telefono").show();
                	$(".field-convolontarioingaggio-email").show();
                }',
            ],
        ]);

    ?>

    <?= $form->field($model, 'nome')->textInput() ?>
    <?= $form->field($model, 'cognome')->textInput() ?>
    <?= $form->field($model, 'telefono')->textInput() ?>
    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'refund')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Aggiungi', ['class' => 'btn btn-success']) ?>
    </div>

    <?php 
    ActiveForm::end(); 
    ?>

</div>

<?php 
$js = '$("#addformvolontarioingaggio").on("beforeSubmit", function(e) {
	e.preventDefault();
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: formData,
        success: function (data) {
            $("#modal-volontario").modal("hide");
            window.reload_volontari();
        },
        error: function (e) {
            console.log(\'errore\',e);
        }
    });
}).on("submit", function(e){
    e.preventDefault();
});';
$this->registerJs($js, $this::POS_READY);
?>


