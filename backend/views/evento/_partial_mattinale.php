<?php
use common\models\ConOperatoreTask;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */
/* @var $form yii\widgets\ActiveForm */

$js = "
$(document).on('click', '.mattinaleModal', function(e) { 
    e.preventDefault();
    $('#modal-mattinale').modal('show');
});
";

$this->registerJs($js, $this::POS_READY);

$heading = '<h3 class="panel-title"><i class="glyphicon glyphicon-globe"></i> '.Html::encode('Diario dell\'evento - Lista attività svolte').'</h3>';

if($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('createTaskEvento')) :

    $heading .= Html::button(
        '<i class="glyphicon glyphicon-plus"></i> Nuova attività',
        [
            'title' => Yii::t('app', 'Aggiungi attività'),
            'class' => 'mattinaleModal btn btn-success',
        ]
    );
endif;
?>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

        <?= GridView::widget([
            'dataProvider' => $tasksDataProvider,
            'filterModel' => $tasksSearchModel,
            'responsive'=>true,
            'hover'=>true,
            'perfectScrollbar' => true,
            'perfectScrollbarOptions' => [],
            'export' => Yii::$app->user->can('exportData') ? [] : false,
            'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
            'panel' => [
                'heading' => $heading,
                'type'=>'default',
                'before' => $this->render('_search_partial_mattinale', ['model' => $tasksSearchModel]),
                
            ],
            
            'columns' => [
                [
                    'format' => 'raw',
                    'attribute' => 'dataora',
                    'contentOptions' => ['style'=>'max-width: 80px; white-space: normal; overflow: auto; word-wrap: break-word;'],
                    'value' => function($model){
                        return Yii::$app->formatter->asDatetime($model->dataora);
                    }
                ],
                [
                    'attribute' => 'idoperatore',
                    'label' => 'Operatore',
                    'contentOptions' => ['style'=>'max-width: 100px; white-space: normal; overflow: auto; word-wrap: break-word;'],
                    'value' => function($model){
                        return Html::encode( @$model->operatore->anagrafica->nome . " " . @$model->operatore->anagrafica->cognome );
                    }
                ],
                [
                    'class' => 'kartik\grid\DataColumn',
                    'attribute' => 'funzioneSupporto.descrizione',
                    'label' => 'Funzione di supporto',
                    'contentOptions' => ['style'=>'max-width: 150px; white-space: normal; overflow: auto; word-wrap: break-word;']
                ],
                [
                    'class' => 'kartik\grid\DataColumn',
                    'attribute' => 'task.descrizione',
                    'label' => 'Attività operativa',
                    'contentOptions' => ['style'=>'max-width: 150px; white-space: normal; overflow: auto; word-wrap: break-word;']
                ],
                [
                    'class' => 'kartik\grid\DataColumn',
                    'attribute' => 'note',
                    'contentOptions' => ['style'=>'max-width: 150px; white-space: normal; overflow: auto; word-wrap: break-word;']
                ],
            ],
        ]); ?>
    </div>
</div>

<?php
Modal::begin([
    'id' => 'modal-mattinale',
    'header' => '<h2>NUOVA VOCE DIARIO EVENTO</h2>',
    'size' => 'modal-lg',
]);

$task = new ConOperatoreTask();

echo Yii::$app->controller->renderPartial('_form_task', ['evento' => $model, 'model' => $task]);
Modal::end();
?>

<?php
$js = '$("#formUpdateDos").on("beforeSubmit", function(e) {
	e.preventDefault();
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: formData,
        success: function (response) {
            if(response.code == 200){
                $("#error-msg").addClass("hide");
                $("#modal-update-dos").find("input,textarea,select").val("").end();
                $("#modal-update-dos").modal("hide");
            }else{
                $("#error-msg").removeClass("hide");  
            }
        },
        error: function (e) {
            console.log("errore", e);
            $("#error-msg").removeClass("hide");
        }
    });
}).on("submit", function(e){
    e.preventDefault();
});';
$this->registerJs($js, $this::POS_READY);
?>
