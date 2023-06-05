<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
use kartik\widgets\Select2;
use kartik\depdrop\DepDrop;
use common\models\geo\GeoLayer;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\geo\GeoQuery;

/* @var $this yii\web\View */
/* @var $model common\models\RichiestaCanadair */
/* @var $form yii\widgets\ActiveForm */

$groups = "SELECT distinct \"group\" FROM geo_query;";
$results = Yii::$app->db->createCommand($groups)->queryAll();
$group_data = [];
foreach ($results as $gr) {
    $group_data[$gr['group']] = $gr['group'];
}

$fields = [];
if(!empty($model->layer)) {
    $ll = GeoLayer::findOne(['layer_name'=>$model->layer]);
    $fields = ['Nessuno'=>'Nessuno'];
    
    
    foreach ($ll->fields as $key => $value) {
        if($key != $ll->geometry_column) $fields[$key] = $key;
    }
    
}

?>

<div class="layers-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput()->label('Nome query') ?>

    <?= $form->field($model, 'layer')->widget(Select2::classname(), [
        'options' => ['id' => 'geoquery-layer', 'placeholder' => 'Seleziona strato...'],
        'data' => ArrayHelper::map(GeoLayer::find()->asArray()->orderBy(['layer_name'=>SORT_ASC])->all(), 'layer_name', 'layer_name')
    ]);
    ?>

    <?= $form->field($model, 'query_type')->widget(Select2::classname(), [
        'options' => ['placeholder' => 'Seleziona...'],
        'data' => GeoQuery::queryTypes()
    ])->label('Tipo di query');
    ?>

    <?= $form->field($model, 'result_type')->widget(Select2::classname(), [
        'options' => ['placeholder' => 'Seleziona...'],
        'data' => GeoQuery::resultType()
    ])->label('Tipo di risultato');
    ?>

    <?= $form->field($model, 'n_geometries')->textInput()->label('Numero di feature da restituire (gestito solo in caso di risultato n punti/aree)') ?>

    <?= $form->field($model, 'buffer')->textInput()->label('Buffer in metri (gestito solo in caso di query INTERSEZIONE CON BUFFER)') ?>

    <?php 
    echo $form->field($model, 'show_distance')->checkBox([
        'label' => 'Mostra distanza',
    ]);
    ?>

    <?php 
    echo $form->field($model, 'enabled')->checkBox([
        'label' => 'Query attiva',
    ]);
    ?>

    <?= $form->field($model, 'result_position')->widget(Select2::classname(), [
        'options' => ['placeholder' => 'Seleziona...'],
        'data' => GeoQuery::positions()
    ])->label('Posizione');
    ?>

    <?= $form->field($model, 'group')->widget(Select2::classname(), [
        'options' => ['placeholder' => 'Seleziona o inserisci raggruppamento...'],
        'pluginOptions' => [
            'tags' => true
        ],
        'data' => $group_data
    ])->label('Raggruppamento');
    ?>

    <?php echo $form->field($model, 'layer_return_field')->widget(DepDrop::classname(), [
             'type' => DepDrop::TYPE_SELECT2,
             'options' => ['id'=>'layer_return_field'],
             'data' => $fields,
             'select2Options' => ['pluginOptions' => ['allowClear' => true]],
             'pluginOptions'=>[
                'allowClear' => true,
                'depends'=>['geoquery-layer'],
                'placeholder' => 'Seleziona campo...',
                'url' => Url::to(['/geo/list-layer-fields'])
             ]
         ])->label('Campo dello strato da restituire'); ?>

    <div class="form-group">
        <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
