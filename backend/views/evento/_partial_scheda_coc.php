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
use kartik\widgets\DateTimePicker;

use yii\helpers\Url;
use yii\widgets\Pjax;

use common\models\EvtSchedaCoc;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */
/* @var $form yii\widgets\ActiveForm */


$js = "
window.reload_files = function(){
    $.pjax.reload({ container:'#lista-file-pjax', timeout:20000 })
}
$(document).on('click', '.fileModal', function(e) { 
    e.preventDefault();
    $('#modal-file').modal('show');
});

";

$this->registerJs($js, $this::POS_READY);



$scheda = $model->schedacoc;

if(empty($scheda)) {
    $scheda = new EvtSchedaCoc;
    $scheda->id_evento = $model->id;
    $actioForm = 'evento/create-scheda-coc?id_evento='.$model->id;
} else {
    $actioForm = 'evento/update-scheda-coc?id_evento='.$model->id.'&id=' . $scheda->id;
    
    if(!empty($scheda->data_apertura)){
        $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $scheda->data_apertura);
        $scheda->data_apertura = $dt->format('d-m-Y H:i');
    }

    if(!empty($scheda->data_chiusura)){
        $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $scheda->data_chiusura);
        $scheda->data_chiusura = $dt->format('d-m-Y H:i');
    }
}

$form = ActiveForm::begin([
    'action' =>[$actioForm]
]);

?>
<div class="row m5w m20h bg-grayLighter box_shadow">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
        <h5 class="m10h text-uppercase color-gray">Scheda COC</h5>
        <div class="row">
            <div class="col-xs-6">
                <?php echo $form->field($scheda, 'data_apertura', ['options' => ['class' => '']])->widget(DateTimePicker::classname(), [
                    'options' => ['placeholder' => 'data apertura'],
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy H:ii',
                        'todayHighlight' => true,
                        'autoclose'=>true
                    ]
                ])->label('Data apertura'); ?>
            </div>
            <div class="col-xs-6">
                <?php echo $form->field($scheda, 'data_chiusura', ['options' => ['class' => '']])->widget(DateTimePicker::classname(), [
                    'options' => ['placeholder' => 'data chiusura'],
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy H:ii',
                        'todayHighlight' => true,
                        'autoclose'=>true
                    ]
                ])->label('Data chiusura'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6">
                <?php echo $form->field($scheda, 'num_atto')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <?php echo $form->field($scheda, 'note')->textarea(['rows' => 6]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


<?php 
if(Yii::$app->user->can('updateSchedaCoc') && !empty($scheda->id)) {
    echo Html::button(
                '<i class="glyphicon glyphicon-plus"></i> Nuovo documento',
                [
                    'title' => Yii::t('app', 'Nuovo documento'),
                    'class' => 'fileModal btn btn-success'
                ]
            );
}
?>

<?php 
if($scheda->id) {

    echo GridView::widget([
        'id' => 'lista-file',
        'dataProvider' => new ActiveDataProvider([
            'query' => $scheda->getDocumenti(),
            'pagination' => false,
        ]),
        'responsive'=>true,
        'hover'=>true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => false,
        'panel' => [
        ],
        'pjax'=>true,
        'pjaxSettings'=>[
            'neverTimeout'=> true,
        ],
        'columns' => [
            [
                'attribute' => 'media',
                'label' => 'File',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 150px;'],
                'value' => function($data){
                    return Html::a(
                            'Scarica allegato ' . $data->uplMedia->id . ' - ' . date("d-m-Y", strtotime($data->uplMedia->date_upload)) . ' <i class="fa fa-download p5w"></i>',
                            ['/media/view-media', 'id' => $data->uplMedia->id],
                            ['class' => 'btn btn-info btn-block' ,'target' => '_blank','data-pjax'=>0]
                        );
                }
            ],
            [
                'attribute' => 'note',
                'label' => 'Note',
                'format' => 'raw',
                'contentOptions' => ['style'=>'max-width: 500px; white-space: normal; word-wrap: break-word;'],
                'value' => function($data){
                    return $data->note;
                }
            ]
        ],
    ]);
}
?>



<?php
if(Yii::$app->user->can('updateSchedaCoc')) : 
    Modal::begin([
        'id' => 'modal-file',
        'header' => '<h2>Inserisci un documento</h2>',
        'size' => 'modal-lg',
    ]);

    $media = new \common\models\ConSchedaCocDocumenti;
    
    $form = ActiveForm::begin([
        'action' =>['evento/add-scheda-coc-documento?id_evento='.$model->id.'&id_scheda='.$scheda->id]
    ]);
    
    echo $form->field($media, 'attachment',['options' => ['class'=>'col-lg-12 no-pl no-pr']])->widget(FileInput::classname(), [
                        'options' => ['accept' => 'application/pdf'],
                    ])->label('Allega file (pdf)'); 
    echo $form->field($media, 'note')->textarea(['rows' => 6]) ;

    echo Html::submitButton('Carica', ['class' => 'btn btn-success']);

    ActiveForm::end();
    
    Modal::end();
endif;
?>


