<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\ViewRubrica;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;

use common\models\LocProvincia;
use common\models\LocComune;
/* @var $this yii\web\View */
/* @var $searchModel common\models\MasMessageTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$js = "

var done_ = false, no_more = false;

function sendAction(form, e) {

    var el__ = document.getElementById('loading-spin');
    el__.style.display = 'block';

	console.log('form', form)
	var check_inputs = document.getElementsByName('check');

	var el = document.getElementsByClassName(\"select-on-check-all\");
	var selected_all = el[0].checked;

	if(!selected_all) {
		var vals = [];
		var row_checks = document.getElementsByClassName(\"kv-row-checkbox\");
		
		for (i = 0; i < row_checks.length; i++) {
			if(row_checks[i].checked) vals.push(row_checks[i].value);
		}

		vals = vals.join(\",\");
	} else {
		vals = 'selected_all';
	}
	
	if(check_inputs.length > 0) {
		check_inputs.forEach( function (inp) {
			inp.value = vals;
		})
	}

	for (i = 0; i < check_inputs.length; i++) {
		check_inputs[i].value = vals
	}

	// fire dell'evento
	
	//console.log('valori inseriti', vals);
}


$('#add_form').parent('form').submit(function(e) {
	if(!done_) {
		e.preventDefault();
		sendAction(null, e);
		done_ = true;
		$(this).submit();
		//done_ = false;
	} else {
        if(no_more) {
            e.preventDefault();
        } else {
            no_more = true;
        }
    }
});
$('#remove_form').parent('form').submit(function(e) {
	if(!done_) {
		e.preventDefault();
		sendAction(null, e);
		done_ = true;
		$(this).submit();
		//done_ = false;
	} else {
        if(no_more) {
            e.preventDefault();
        } else {
            no_more = true;
        }
    }
	
});
";

$this->registerJs($js, $this::POS_READY);

$cols = [
            [

                'class' => 'kartik\grid\CheckboxColumn',
                
                'checkboxOptions' => function($model, $key, $index, $widget) {
                    return ['value' => $model->id_riferimento . "|" . $model->tipo_riferimento];
                }

            ],
            [
                'attribute'=>'added',
                'width'=>'150px',
                'label' => 'Inserito',
                'format' => 'raw',
                'value'=>function($model_) use ($model) {
                	return $model_->getGruppo()->where(['id'=>$model->id])->count() > 0 ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-close text-danger"></i>';
                }
            ],
            [
                'attribute'=>'valore_riferimento',
                'label' => 'Riferimento'
            ],
            [
                'attribute'=>'valore_contatto',
                'label' => 'Contatto'
            ],
            [
                'attribute'=>'tipo_contatto',
                'width'=>'150px',
                'label' => 'Tipo contatto',
                'filter'=> Html::activeDropDownList($searchModel, 'tipo_contatto', ViewRubrica::getTipi(), ['class' => 'form-control','prompt' => 'Tutti']),
                'value'=>function($model) {
                    return $model->tipo();
                }
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

	<?php if(Yii::$app->user->can('updateRubricaGroup')) { ?>
		<h3>Inserisci nel gruppo</h3>
		<p>Se selezionati: </p>
	    <div style="margin-bottom: 10px;">
		    <div style="width: 100px; display: inline-block;">
			    <?=Html::beginForm();?>
			    <?=Html::hiddenInput('action', 'add')?>
			    <?=Html::hiddenInput('check', '', ['id'=>'add_checks'])?>
			    <?=Html::submitButton('Aggiungi', ['class' => 'btn btn-success', 'id'=>'add_form']); ?>
			    <?=Html::endForm();?>
			</div>
			<div style="width: 100px; display: inline-block;">
			    <?=Html::beginForm();?>
			    <?=Html::hiddenInput('action', 'remove')?>
			    <?=Html::hiddenInput('check', '', ['id'=>'remove_checks'])?>
			    <?=Html::submitButton('Rimuovi', ['class' => 'btn btn-danger', 'id'=>'remove_form']); ?>
			    <?=Html::endForm();?>
			</div>
		</div>

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
        'pjax'=>true,
        'pjaxSettings'=>[
            'neverTimeout'=> true,
        ],
        'panel' => [
            'before'=> Html::a('<i class="glyphicon glyphicon-repeat"></i> Azzera filtri', ['view', 'id'=>$model->id], ['class' => 'btn btn-info m10w']),
            'heading'=> "Scarica rubrica completa " . ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $cols,
                'target' => ExportMenu::TARGET_BLANK,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_HTML => false
                ]                
            ]),
        ],
        'columns' => $full_cols
    ]); ?>
</div>
