<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\ViewRubrica;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;

use common\models\LocProvincia;
use common\models\LocComune;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel common\models\MasMessageTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$cols = [
            [

                'class' => 'kartik\grid\CheckboxColumn',
                'header' => Html::checkBox('selection_all', false, [
                    'class' => 'contact-select-on-check-all'
                ]),
                'checkboxOptions' => function($model, $key, $index, $widget) {
                    return ['value' => $model->id_riferimento . "|" . $model->tipo_riferimento,'class'=>'contact-kv-row-checkbox'];
                }

            ],
            [
                'attribute'=>'valore_riferimento',
                'label' => 'Riferimento'
            ],
            [
                'label' => 'Comune',
                'attribute' => 'comune'                
            ],
            [
                'label' => 'Provincia',
                'attribute' => 'provincia',
                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                'filter' => array_merge([''=>'Tutti'], ArrayHelper::map(LocProvincia::find()
                    ->where([
                                Yii::$app->params['region_filter_operator'], 
                                'id_regione', 
                                Yii::$app->params['region_filter_id']
                            ])
                    ->all(), 'sigla', 'sigla')),
            ],
            [
                'attribute'=>'tipologia_riferimento',
                'label' => 'Tipo riferimento',
                'filter'=> Html::activeDropDownList($searchModel, 'tipologia_riferimento', ViewRubrica::getTipiRiferimento(), ['class' => 'form-control','prompt' => 'Tutti'])
            ]
            
        ];



$full_cols = $cols;


?>
<div class="mass-message-template-index">
    <h3>Seleziona i contatti singolarmente</h3>
	
    <?php if(!empty($model->allerta) && $model->allerta->lat && $model->allerta->lon) { ?>

        <p>Filtra per distanza dal punto di allerta</p>
        <?php $form = ActiveForm::begin([
            'action' => ['create-invio', 'id_messaggio'=>$model->id],
            'method' => 'get',
        ]); ?>

        <?= $form->field($searchModel, 'distance')->label('Distanza (km)') ?>
        <?php 
            echo Html::hiddenInput('ViewRubricaSearch[lat]', $model->allerta->lat);
            echo Html::hiddenInput('ViewRubricaSearch[lon]', $model->allerta->lon);
        ?>
        <div class="form-group">
            <?= Html::submitButton('Cerca', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('Annulla', ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    <?php } ?>

    <?= GridView::widget([
        'id' => 'lista-rubrica',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive'=>true,
        'hover'=>true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'panel' => [
            'before'=> Html::a('<i class="glyphicon glyphicon-repeat"></i> Azzera filtri', ['create-invio', 'id_messaggio'=>$model->id], ['class' => 'btn btn-info m10w']),
            'heading'=> "Elenco contatti",
        ],
        'columns' => $full_cols
    ]); ?>
</div>
