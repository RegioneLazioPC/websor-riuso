<?php
use common\models\ConOperatoreTask;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */
/* @var $form yii\widgets\ActiveForm */

$js = "
$(document).on('click', '.mattinaleModal', function(e) { 
    e.preventDefault();
    $('#modal-mattinale').modal('show');
});
$(document).on('click', '.photoModal', function(e) { 
    e.preventDefault();
    $('#modal-gallery').modal('show');
});

";

$this->registerJs($js, $this::POS_READY);

?>
<script>
function openFullscreen(element_id) {
  
  var elem = document.getElementById(element_id);
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.mozRequestFullScreen) { /* Firefox */
    elem.mozRequestFullScreen();
  } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari and Opera */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE/Edge */
    elem.msRequestFullscreen();
  }
}
</script>


<?php
$heading = '<h3 class="panel-title"><i class="glyphicon glyphicon-globe"></i> '.Html::encode('Diario dell\'evento - Lista attività svolte').'</h3>';

if(
    ($this->context->action->id == 'gestione-evento' && Yii::$app->user->can('createTaskEvento')) ||
    ($model->stato == 'Chiuso' && Yii::$app->user->can('createTaskEvento') && Yii::$app->user->can('Admin'))    
) :

    $heading .= Html::button(
        '<i class="glyphicon glyphicon-plus"></i> Nuova attività',
        [
            'title' => Yii::t('app', 'Aggiungi attività'),
            'class' => 'mattinaleModal btn btn-success',
        ]
    );
endif;


$medias = Yii::$app->db->createCommand("SELECT m.*,
    s.id as id_segnalazione,
    o.ref_id,
    o.denominazione,
    s.dataora_segnalazione,
    s.nome_segnalatore,
    s.cognome_segnalatore,
    s.telefono_segnalatore,
    e.num_protocollo
    FROM upl_media m
    LEFT JOIN con_upl_media_utl_segnalazione cs ON cs.id_media = m.id 
    LEFT JOIN utl_segnalazione s ON s.id = cs.id_segnalazione
    LEFT JOIN con_evento_segnalazione ce ON ce.idsegnalazione = s.id 
    LEFT JOIN utl_evento e ON e.id = ce.idevento
    LEFT JOIN vol_organizzazione o ON o.id = s.id_organizzazione
    WHERE 
    m.id_tipo_media = (SELECT id FROM upl_tipo_media WHERE descrizione = 'Immagine segnalazione') 
    AND (e.id = :event_id OR e.idparent = :event_id);", ['event_id' => $model->id]
)->queryAll();


?>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        
        <?php 

        if(count($medias) > 0) {
            echo Html::button(
                '<i class="glyphicon glyphicon-camera"></i> Galleria immagini segnalazioni',
                [
                    'title' => Yii::t('app', 'Galleria immagini'),
                    'class' => 'photoModal btn btn-info m10h',
                ]
            ); 
        }
        ?>

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
            'rowOptions'=>function($model){
                if($model->manual_flag == 1) return ['class' => 'green-td'];

                return null;
            },
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
                        return (!empty($model->operatore)) ? Html::encode( @$model->operatore->anagrafica->nome . " " . @$model->operatore->anagrafica->cognome ) : 'CHIUSO DA SCRIPT';
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
                    'format' => 'raw',
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

Modal::begin([
    'id' => 'modal-gallery',
    'header' => '<h2>GALLERIA IMMAGINI SEGNALAZIONI</h2>',
    'size' => 'modal-lg',
]);

?>
<div class="row">
    <?php 
    foreach ($medias as $media) {
        /*
            utl_segnalazione.id as id_segnalazione,
            vol_organizzazione.ref_id,
            vol_organizzazione.denominazione,
            utl_segnalazione.dataora_segnalazione,
            utl_segnalazione.nome_segnalatore,
            utl_segnalazione.cognome_segnalatore,
            utl_segnalazione.telefono_segnalatore,
            utl_evento.num_protocollo
         */
        ?>
        <div class="row">
            <div class="col-xs-6">
                <img onclick="openFullscreen('img_gallery_<?php echo $media['id'];?>')" id="img_gallery_<?php echo $media['id'];?>" src="<?php echo Url::to(['/media/view-media', 'id' => $media['id']]); ?>" style="width: 100%; height: auto; display: block; cursor: pointer;" />
            </div>
            <div class="col-xs-6">
                <b>Id segnalazione</b> <a target="_blank" href="<?php echo Url::to(['/segnalazione/view', 'id' => $media['id_segnalazione']]);?>"><?php echo $media['id_segnalazione'];?></a><br />
                <b>Data ora</b> <?php echo $media['dataora_segnalazione'];?><br />
                <b>Num. elenco territoriale</b> <?php echo $media['ref_id'];?><br />
                <b>ODV</b> <?php echo $media['denominazione'];?><br />
                <b>Segnalante</b> <?php echo $media['nome_segnalatore'] . " " . $media['cognome_segnalatore'] . " " .$media['telefono_segnalatore'];?><br />
                <b>Prot. <?php echo ($media['num_protocollo'] == $model->num_protocollo) ? 'evento' : 'fronte';?></b> <?php echo $media['num_protocollo'];?><br />
            </div>
        </div>
        <?php
    }
    ?>
</div>
<?php

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
