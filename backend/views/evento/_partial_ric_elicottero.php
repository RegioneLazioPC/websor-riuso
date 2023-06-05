<?php
use common\models\ComComunicazioni;
use common\models\LocProvincia;
use common\models\RichiestaElicottero;
use common\models\RichiestaMezzoAereo;
use common\models\UtlTipologia;
use kartik\grid\GridView;
use yii\base\DynamicModel;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use common\models\UtlIngaggio;
use common\models\UtlIngaggioSearchForm;
use yii\helpers\Url;
use yii\widgets\Pjax;
use kartik\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */
/* @var $form yii\widgets\ActiveForm */


$js = "
    window.reload_elicottero = function(){
        $.pjax.reload({ container:'#lista-elicotteri-pjax', timeout:20000 })
    }
    $(document).on('click', '.ricElicotteroBtn', function(e) { 
        e.preventDefault();
        $('#modal-ric-elicottero').modal('show');
    });
    $(document).on('click', '.updateElicottero', function(e) { 
        e.preventDefault();
        
        $('#modal-update-elicottero')
        .modal('show').find('.modal-body').load($(this).attr('location'), function() {
                jQuery(\"#richiestaelicottero-date\").kvDatepicker({
                    format: \"dd-mm-yyyy\",
                    language: \"it\",
                    autoclose: true
                });
                jQuery(\"#richiestaelicottero-date_arrivo_stimato\").kvDatepicker({
                    format: \"dd-mm-yyyy\",
                    language: \"it\",
                    autoclose: true
                });
                jQuery(\"#richiestaelicottero-date_atterraggio\").kvDatepicker({
                    format: \"dd-mm-yyyy\",
                    language: \"it\",
                    autoclose: true
                });
        });
    })

    $(document).on('click', '.schedaCoau', function(e) { 
        e.preventDefault();
        console.log(e, \$(this))
        $('#modal-richiesta_coau_anteprima_'+\$(this).attr('data-id')).modal('show');
    });

    $(document).ready(function(){
        $('#modal-update-elicottero').removeAttr('tabindex');
});
";

$this->registerJs($js, $this::POS_READY);


$heading = '<h3 class="panel-title"><i class="fas fa-list"></i>  '.Html::encode('Lista richieste elicotteri').'</h3>';


if($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('createRichiestaElicottero')) $heading .= Html::button(
                '<i class="glyphicon glyphicon-plus"></i> Nuova richiesta elicottero',
                [
                    'title' => Yii::t('app', 'Nuova richiesta Elicottero'),
                    'class' => 'ricElicotteroBtn btn btn-success'
                ]
            );

?>

<?= GridView::widget([
    'id' => 'lista-elicottero',
    'dataProvider' => $ricElicotteroDataProvider,
    'filterModel' => $ricElicotteroSearchModel,
    'responsive'=>true,
    'hover'=>true,
    'perfectScrollbar' => true,
    'perfectScrollbarOptions' => [],
    'export' => Yii::$app->user->can('exportData') ? [] : false,
    'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
    'panel' => [
        'heading'=>$heading,
    ],
    'pjax'=>true,
    'pjaxSettings'=>[
        'neverTimeout'=> true,
    ],
    'rowOptions'=>function($model){
        $class = null;
        
        if($model->deleted == 1) $class = ['class' => 'red-td'];
        return $class;
    },
    'columns' => [
        [
            'attribute' => 'created_at',
            'label' => 'Data Creazione',
            'format' => 'raw',
            'contentOptions' => ['style'=>'width: 80px;'],
            'value' => function($data){
                return Yii::$app->formatter->asDatetime($data->created_at);
            }
        ],
        [
            'attribute' => 'idoperatore',
            'label' => 'Operatore',
            'format' => 'raw',
            'value' => function($data){
                return (!empty($data->operatore) && !empty($data->operatore->anagrafica)) ? Html::encode( $data->operatore->anagrafica->nome .' '. $data->operatore->anagrafica->cognome) : "";
            }
        ],
        [
            'attribute' => 'engaged',
            'label' => 'Ingaggiato',
            'format' => 'raw',
            'value' => function($data){
                if(!$data->edited) return "Da lavorare";
                return ($data->engaged) ? "Si" : "No";
            }
        ],
        [
            'attribute' => 'elicottero',
            'label' => 'Codice',
            'format' => 'raw',
            'value' => function($data){

                if(!empty($data->codice_elicottero)) return Html::encode($data->codice_elicottero);

                if(!empty($data->id_elicottero)) return Html::encode($data->elicottero->targa);

                return " - ";

                
            }
        ],
        [
            'attribute' => 'dataora_decollo',
            'label' => 'Decollo',
            'format' => 'raw',
            'value' => function($data){
                if(!empty($data->dataora_decollo) ) {
                    $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $data->dataora_decollo);
                    return $dt->format('d-m-Y H:i');
                }
                return " - ";
            }
        ],
        [
            'attribute' => 'note',
            'label' => 'Note',
            'contentOptions' => ['style'=>'max-width: 200px; white-space: normal; word-wrap: break-word;'],
            'value' => function($data){
                return $data->note;
            }
        ],
        [
            'attribute' => 'id',
            'label' => 'COAU',
            'format' => 'raw',
            'value' => function($data){
                $just_sent = !empty( $data->id_anagrafica_funzionario ) ? " Mail inviata" : "";
                return ( $this->context->action->id == 'gestione-evento' && Yii::$app->user->can('sendRichiestaElicotteroToCOAU') && 
                    $data->engaged ) ? Html::button(
                    'Scheda COAU',
                    [
                        'title' => Yii::t('app', 'Anteprima scheda COAU'),
                        'data-id' => $data->id,
                        'class' => 'schedaCoau btn btn-info btn-xs',
                        'style' => 'margin-left: 12px'
                    ]
                ) . " " . $just_sent : "".$just_sent;
            }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template'=>'{update} {annullate}',
            'buttons' => [
                'update' => function ($url, $model) {
                    $icon = $model->deleted == 1 ? 'fa-eye' : 'fa-pencil';
                    return ( $this->context->action->id == 'gestione-evento' &&  (Yii::$app->user->can('updateRichiestaElicottero') || Yii::$app->user->can('updatePartialRichiestaElicottero')) ) ? Html::a('<span class="fa '. $icon .'"></span>&nbsp;&nbsp;',
                        '#',
                        [
                            'title' => Yii::t('app', 'Gestione richiesta elicottero'),
                            'data-toggle'=>'tooltip',
                            'class' => 'updateElicottero',
                            'location' => 'update-elicottero?id='.$model->id
                        ]) : "";
                },
                'annullate' => function ($url, $model) {
                    return ($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('annullateRichiestaElicottero') && !$model->engaged && $model->edited != 1 && $model->deleted != 1) ? Html::a('<span class="fa fa-trash"></span>&nbsp;&nbsp;',
                        ['evento/annulla-richiesta-elicottero?id='.$model->id],
                        [
                            'title' => Yii::t('app', 'Annulla richiesta elicottero'),
                            'data-toggle'=>'tooltip',
                            'class' => '',
                            'data' => [
                                'confirm' => "Sicuro di voler annullare la richiesta?"
                            ]
                        ]) : "";
                },
            ],
        ],
    ],
]);
?>



<?php
if($this->context->action->id == 'gestione-evento' && (
        Yii::$app->user->can('updateRichiestaElicottero') || 
        Yii::$app->user->can('updatePartialRichiestaElicottero')
    )
) {
    foreach (
        $ricElicotteroSearchModel->find()
        ->where(['idevento'=>$model->id])
        ->andWhere(['engaged'=>true])
        ->all() as $richiesta) {
        
        Modal::begin([
            'id' => 'modal-richiesta_coau_anteprima_'.$richiesta->id,
            'header' => '<h2 class="p10w"><i class="fas fa-eye"></i> ANTEPRIMA SCHEDA COAU</h2>',
            'size' => 'modal-lg',
        ]); 
            
        echo Yii::$app->controller->renderPartial('_partial_scheda_coau', [
            'evento' => $model,
            'is_applicativo' => true,
            'richiesta' => $richiesta
        ]);

        Modal::end();
    }
}
?>

<?php

if($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('createRichiestaElicottero')) {
    
        Modal::begin([
            'id' => 'modal-ric-elicottero',
            'header' => '<h2>NUOVA RICHIESTA ELICOTTERO</h2>',
            'size' => 'modal-lg',
        ]);

        $ricElicottero = new RichiestaElicottero();

        echo Yii::$app->controller->renderPartial('_form_ric_elicottero', ['model' => $ricElicottero, 'evento' => $model]);
        Modal::end();

}


if($this->context->action->id == 'gestione-evento' &&  (
        Yii::$app->user->can('updateRichiestaElicottero') || 
        Yii::$app->user->can('updatePartialRichiestaElicottero')
    )
) :
    // MODAL UPDATE RICH ELICOTTERO
    Modal::begin([
        'id' => 'modal-update-elicottero',
        'header' => '<h2 class="p10w"><i class="fas fa-edit"></i> AGGIORNA ELICOTTERO</h2>',
        'size' => 'modal-lg'
    ]);

    
    Modal::end();

endif;
?>


