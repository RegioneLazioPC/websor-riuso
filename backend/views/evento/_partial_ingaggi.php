<?php
use common\models\ComComunicazioni;
use common\models\LocProvincia;
use common\models\RichiestaMezzoAereo;
use common\models\UtlTipologia;
use common\models\UtlAutomezzoTipo;
use common\models\UtlAttrezzaturaTipo;
use kartik\grid\GridView;
use yii\base\DynamicModel;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use common\models\UtlIngaggio;
use common\models\UtlIngaggioSearchForm;
use yii\helpers\Url;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */
/* @var $form yii\widgets\ActiveForm */


$js = "
window.reload_ingaggi = function(){
    $.pjax.reload({ container:'#lista-ingaggi-pjax', timeout:20000 })
}
$(document).on('click', '.popupIngaggiModal', function(e) { 
    e.preventDefault();
    $('#modal-ingaggio').modal('show');
});
";

$this->registerJs($js, $this::POS_READY);

$heading = '<h3 class="panel-title"><i class="fas fa-list"></i>  '.Html::encode('Lista attivazioni').'</h3>';

if($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('createIngaggio')) :
    $heading .= Html::button(
                            '<i class="glyphicon glyphicon-plus"></i> Nuova attivazione',
                            [
                                'title' => Yii::t('app', 'Aggiungi intervento'),
                                'class' => 'popupIngaggiModal btn btn-success',
                            ]
                        );

    $heading .= '<span class="carto-link">'. Html::a('Cartografia', ['/sistema-cartografico?evt='.$model->id], ['class' => 'btn btn-warning','style'=>'margin-left: 10px']).'</span>';
endif;

$evento = $model;
?>


<?= GridView::widget([
    'id' => 'lista-ingaggi',
    'dataProvider' => $ingaggiDataProvider,
    'filterModel' => $ingaggiSearchModel,
    'responsive'=>true,
    'hover'=>true,
    'perfectScrollbar' => true,
    'perfectScrollbarOptions' => [],
    'export' => Yii::$app->user->can('exportData') ? [] : false,
    'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
    'panel' => [
        'heading'=>$heading,
        'before' => $this->render('_search_partial_ingaggi', [
            'model' => $ingaggiSearchModel, 
            'evento' => $model,
            'view' => isset($hide_btn) ? 'view' : 'create-task']),
        
    ],
    'pjax'=>true,
    'pjaxSettings'=>[
        'neverTimeout'=> true,
    ],
    'rowOptions'=>function($model){
            $class = null;
            return ['class'=>$model->getStatoColor().'-td'];
        },
    'columns' => [
        
        [
            'attribute' => 'created_at',
            'label' => 'Creazione',
            'format' => 'raw',
            'contentOptions' => ['style'=>'width: 80px;']
        ],
        [
            'label' => 'Mezzo',
            'attribute' => 'automezzo.tipo.descrizione',
            'contentOptions' => ['style'=>'width: 150px; white-space: unset;'],
            'filter'=> Html::activeDropDownList($ingaggiSearchModel, 'automezzo.tipo.descrizione', ArrayHelper::map(UtlAutomezzoTipo::find()->all(), 'id', 'descrizione'), 
                ['class' => 'form-control','prompt' => 'Tutti']),
            'value' => function($data){
                if(!empty($data->automezzo)){
                    return $data->automezzo->targa . ' - ' . $data->automezzo->tipo->descrizione;
                }
            }
        ],
        [
            'label' => 'Attrezzatura',
            'attribute' => 'attrezzatura.tipo.descrizione',
            'contentOptions' => ['style'=>'width: 150px; white-space: unset;'],
            'filter'=> Html::activeDropDownList($ingaggiSearchModel, 'attrezzatura.tipo.descrizione', ArrayHelper::map(UtlAttrezzaturaTipo::find()->all(), 'id', 'descrizione'), 
                ['class' => 'form-control','prompt' => 'Tutti']),
            'value' => function($data){
                if(!empty($data->attrezzatura)){
                    return $data->attrezzatura->modello . ' - ' . $data->attrezzatura->tipo->descrizione;
                }
            }
            
        ], 
        [
            'label' => 'Identificativo organizzazione',
            'attribute' => 'organizzazione.ref_id',
            'format' => 'raw',
            'value' => function($data){
                if(!empty($data->sede) && !empty($data->sede->organizzazione)){
                    return Html::encode($data->sede->organizzazione->ref_id);
                }
            }
        ],  
        [
            'label' => 'Organizzazione',
            'attribute' => 'organizzazione.denominazione',
            'contentOptions' => ['style'=>'width: 150px; white-space: unset;'],
            'format' => 'raw',
            'value' => function($data){
                if(!empty($data->sede) && !empty($data->sede->organizzazione)){
                    return Html::encode(@$data->sede->organizzazione->denominazione);
                }
            }
        ],      
        
        [
            'attribute' => 'stato',
            'format' => 'raw',
            'contentOptions' => ['style'=>'width: 200px; white-space: unset;'],
            'value' => function($data){
                if($data->stato != 2) return $data->getStato();

                $ret = $data->getStato();
                $ret .= " - " . $data->getMotivazioneRifiuto();
                if($data->motivazione_rifiuto == 5) $ret .= " - " . $data->motivazione_rifiuto_note;

                return Html::encode($ret);
            }
        ],  
        [
            'attribute' => 'contatti',
            'format' => 'raw',
            'contentOptions' => ['style'=>'width: 200px; white-space: unset;'],
            'label' => 'Contatti',
            'value' => function($data){
                $contatti = $data->organizzazione->contattoAttivazioni;

                if(count($contatti)>0) {
                    $conts = array_values( ArrayHelper::map($contatti, 'id', 'contatto') );
                    
                    return Html::encode( implode(", ", $conts) );
                }

                return "";
            }
        ],      
        [
            'attribute' => 'note',
            'format' => 'raw',
            'contentOptions' => ['style'=>'max-width: 200px; white-space: normal; word-wrap: break-word;'],
            'value' => function($data){
                return sprintf('<a href="#" data-toggle="tooltip" title="%s"><i class="fa fa-info-circle"></i> Dettaglio </a>', 
                    Html::encode(str_replace("\"", "'", $data->note) ) );
            }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template'=>'{update}',

            'buttons' => [

                'update' => function ($url, $model) use ($evento) {
                    return (Yii::$app->user->can('updateIngaggio') && $evento->stato != 'Chiuso') ? Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', ['ingaggio/update', 'id'=>$model->id], [
                        'title' => Yii::t('app', 'Aggiorna attivazione'),
                        'data-toggle'=>'tooltip'
                    ]) : "";
                },
            ],
        ],
    ],
]); ?>



<?php
Modal::begin([
    'id' => 'modal-ingaggio',
    'header' => '<h2>AGGIUNGI ATTIVAZIONE</h2>',
    'size' => 'modal-lg'
]);

$add_model = new UtlIngaggioSearchForm();
$add_model->id_evento = $model->id;
$add_model->lat = $model->lat;
$add_model->lon = $model->lon;

echo Yii::$app->controller->renderPartial('_form_ingaggio', ['model'=> $add_model]);
Modal::end();
?>