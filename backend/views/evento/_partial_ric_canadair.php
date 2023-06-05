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

$(document).on('click', '.canadairModal', function(e) { 
    e.preventDefault();
    $('#modal-canadair').modal('show');
});
$(document).on('click', '.updateCanadair', function(e) { 
    e.preventDefault();
    $('#modal-update-canadair').modal('show').find('.modal-body').load($(this).attr('location'));
});
";

$this->registerJs($js, $this::POS_READY);

$heading = '<h3 class="panel-title"><i class="fas fa-list"></i>  '.Html::encode('Lista richieste Canadair').'</h3>';

if($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('createRichiestaCanadair')) $heading .= Html::button(
                '<i class="glyphicon glyphicon-plus"></i> Nuova richiesta Canadair',
                [
                    'title' => Yii::t('app', 'Nuova richiesta Canadair'),
                    'class' => 'canadairModal btn btn-success'
                ]
            );
?>

<?= GridView::widget([
    'id' => 'lista-ingaggi',
    'dataProvider' => $ricCanadairDataProvider,
    'filterModel' => $ricCanadairSearchModel,
    'responsive'=>true,
    'hover'=>true,
    'perfectScrollbar' => true,
    'perfectScrollbarOptions' => [],
    'export' => Yii::$app->user->can('exportData') ? [] : false,
    'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
    'panel' => [
        'heading'=>$heading,
        'footer'=>false,
    ],
    'pjax'=>true,
    'pjaxSettings'=>[
        'neverTimeout'=> true,
    ],
    'columns' => [      
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
            'label' => 'Codice',
            'attribute' => 'codice_canadair',
            'contentOptions' => ['style'=>'width: 80px; white-space: normal;']
        ],
        [
            'label' => 'Comunicazione',
            'attribute' => 'comunicazione.oggetto'
        ],
        [
            'label' => 'Note',
            'attribute' => 'comunicazione.contenuto',
            'format' => 'raw',
            'contentOptions' => ['style'=>'width: 300px; white-space: normal;'],
            'value' => function($model) {
                $data = $model->comunicazione->contenuto;
                if(!empty($model->motivo_rifiuto)) {
                    if(!empty($data)) $data .= '<br />';
                    $data .= $model->motivo_rifiuto;
                }
                return Html::encode($data);
            }
        ],


        [
            'class' => 'yii\grid\ActionColumn',
            'template'=>'{update}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return ($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('updateRichiestaCanadair')) ? Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;',
                        '#',
                        [
                            'title' => Yii::t('app', 'Aggiorna richiesta Canadair'),
                            'data-toggle'=>'tooltip',
                            'class' => 'updateCanadair',
                            'location' => 'update-canadair?id='.$model->id
                        ]) : "";
                },
            ],
        ],
    ],
]); ?>



<?php
if($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('createRichiestaCanadair')) : 
    // MODAL SEND MAIL RICHIESTA DOS
    Modal::begin([
        'id' => 'modal-canadair',
        'header' => '<h2 class="p10w"><i class="fas fa-envelope"></i> INVIO MAIL RICHIESTA CANADAIR</h2>',
        'size' => 'modal-lg',
    ]);

    $comunicazione = new ComComunicazioni();

    echo Yii::$app->controller->renderPartial('_form_send_mail_canadair', ['evento' => $model, 'comunicazione' => $comunicazione]);
    Modal::end();
endif;
?>


<?php
if($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('updateRichiestaCanadair')) :
    // MODAL UPDATE DOS
    Modal::begin([
        'id' => 'modal-update-canadair',
        'header' => '<h2 class="p10w"><i class="fas fa-edit"></i> AGGIORNA CANADAIR</h2>',
        'size' => 'modal-lg',
    ]);

    Modal::end();
endif;
?>



