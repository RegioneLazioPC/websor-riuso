<?php
use common\models\ComComunicazioni;
use common\models\LocProvincia;
use common\models\RichiestaDos;
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

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */
/* @var $form yii\widgets\ActiveForm */

$js = "

$(document).on('click', '.dosModal', function(e) { 
    e.preventDefault();
    $('#modal-dos').modal('show');
});
$(document).on('click', '.updateDos', function(e) { 
    e.preventDefault();
    $('#modal-update-dos').modal('show').find('.modal-body').load($(this).attr('location'));
});
";

$this->registerJs($js, $this::POS_READY);

$heading = '<h3 class="panel-title"><i class="fas fa-list"></i> '.Html::encode('Lista richieste DOS').'</h3>';


    if($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('createRichiestaDos')) $heading .= Html::button(
                '<i class="glyphicon glyphicon-plus"></i> Nuova richiesta DOS',
                [
                    'title' => Yii::t('app', 'Nuova richiesta DOS'),
                    'class' => 'dosModal btn btn-success'
                ]
            );
?>

<?= GridView::widget([
    'id' => 'lista-ingaggi',
    'dataProvider' => $dosDataProvider,
    'filterModel' => $dosSearchModel,
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
    
    'columns' => [
        [
            'class' => 'yii\grid\ActionColumn',
            'template'=>'{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return ($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('updateRichiestaDos')) ? Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;',
                        '#',
                        [
                            'title' => Yii::t('app', 'Aggiorna richiesta DOS'),
                            'data-toggle'=>'tooltip',
                            'class' => 'updateDos',
                            'location' => 'update-dos?id='.$model->id
                        ]) : "" ;
                },
            ],
        ],
        [
            'attribute' => 'engaged',
            'label' => 'Ingaggiato',
            'value' => function($data){
                return $data->engaged ? 'Si' : 'No';
            }
        ],
        [
            'label' => 'Cod. DOS',
            'attribute' => 'codicedos'
        ],
        [
            'attribute' => 'created_at',
            'label' => 'Data richiesta',
            'format' => 'raw',
            'value' => function($data){
                return Yii::$app->formatter->asDatetime($data->created_at);
            }
        ],
        [
            'attribute' => 'idoperatore',
            'label' => 'Operatore',
            'format' => 'raw',
            'value' => function($data){
                return Html::encode( $data->operatore->anagrafica->nome .' '. $data->operatore->anagrafica->cognome );
            }
        ],
        [
            'label' => 'Contatto',
            'attribute' => 'comunicazione.contatto'
        ],
        [
            'label' => 'Comunicazione',
            'attribute' => 'comunicazione.oggetto'
        ],
        
        [
            'label' => 'Note',
            'attribute' => 'motivo_rifiuto',
            'format' => 'raw',
            'value' => function($data){
                $str = '<div>';
                if($data->comunicazione && !empty($data->comunicazione->contenuto)) $str.='<p>COMUNICAZIONE: '.Html::encode($data->comunicazione->contenuto) . '</p>';
                if(!empty($data->motivo_rifiuto)) $str.='<p>NOTE: '.Html::encode($data->motivo_rifiuto) . '</p>';
                $str .= '</div>';
                return $str;
            }
        ],
        
    ],
]); ?>



<?php
if($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('createRichiestaDos')) :
    // MODAL SEND MAIL RICHIESTA DOS
    Modal::begin([
        'id' => 'modal-dos',
        'header' => '<h2 class="p10w"><i class="fas fa-envelope"></i> INVIO MAIL RICHIESTA DOS</h2>',
        'size' => 'modal-lg',
    ]);

    $comunicazione = new ComComunicazioni();

    echo Yii::$app->controller->renderPartial('_form_send_mail_dos', ['evento' => $model, 'comunicazione' => $comunicazione]);
    Modal::end();
endif;
?>


<?php
if($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('updateRichiestaDos')) :
    // MODAL UPDATE DOS
    Modal::begin([
        'id' => 'modal-update-dos',
        'header' => '<h2 class="p10w"><i class="fas fa-edit"></i>  AGGIORNA DOS</h2>',
        'size' => 'modal-lg',
    ]);

    Modal::end();
endif;
?>

