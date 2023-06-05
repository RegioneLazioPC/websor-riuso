<?php 
use common\models\ConMezzoSchieramento;
use common\models\UtlAutomezzoSearch;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\UtlCategoriaAutomezzoAttrezzatura;
use common\models\UtlAutomezzoTipo;
use common\models\tabelle\TblTipoRisorsaMeta;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;


use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use kartik\date\DatePicker;



$js = "

$('#modal-add-connection-mezzo').on('hidden.bs.modal', function () {
    $('body').addClass('modal-open');
})
var prevent = false;

$('.close-modal-empty').click(function(e){
    e.preventDefault();
    $('#modal-add-connection-mezzo').modal('hide');
    $('#conmezzoschieramento-id_utl_automezzo').attr('value', null);
});

$( '#add_connection_form' ).submit(function( event ) {
    event.preventDefault();

    if( !$('#conmezzoschieramento-id_utl_automezzo').attr('value') ) return;
    if(prevent) return;

    prevent = true;
    //var form = $(this);
    //var actionUrl = form.attr('action');
    
    $.pjax.submit(event, '#avaible-mezzi-list-pjax', {
        'push': false,
        'replace': false,
        'timeout': 60000,
        'scrollTo': 0,
        'maxCacheLength': 0
    });

    /*
    $.ajax({
        type: 'POST',
        url: actionUrl,
        data: form.serialize(), 
        success: function(data)
        {
            console.log('data', data)
            prevent = false;
            if(data.error) {
                $('#error_message_form').html(data.error)
            } else {
                $('#error_message_form').html('')
                
                
                //$.pjax.reload({ container:'#selected-mezzi-list-pjax', async: false, timeout:false });
                $.pjax.reload({ container: '#mezzi-list-container-grid', async: false, timeout:false });

                $('#modal-add-connection-mezzo').modal('hide');
                $('#conmezzoschieramento-id_utl_automezzo').attr('value', null);
            }
            
        }
    });*/
});


$(document).on('click', '.modalAddConnection', function(e) { 
    e.preventDefault();
    $('#modal-add-connection-mezzo').modal('show');
    $('#conmezzoschieramento-id_utl_automezzo').attr('value', $(this).attr('value'))
});
";

$this->registerJs($js, $this::POS_READY);




$add_js = '';

$scripts = "prevent = false;";
if(isset($reload_pjax_main)) {
    $scripts .= "$.pjax.reload({ container:'#selected-mezzi-list-pjax', timeout:60000 });";
}

if(isset($close_modal_add)) {
    $scripts .= "
        $('#modal-add-connection-mezzo').modal('hide');
        $('#conmezzoschieramento-id_utl_automezzo').attr('value', null);
    ";
}

if(isset($error_message)) {
    $scripts .= " $('#error_message_form').html('".str_replace("'","",$error_message)."');";
}

if(!empty($scripts)) {
    
    $add_js = "<script type=\"text/javascript\">
        $(document).ready(function() {
            ".$scripts."
        });
    </script>";
    
}

$err = '';
if(isset($error_message)) {
    $err = '<p class="text-danger">'.$error_message.'</p>';
}

$cols = [[
        'class' => 'yii\grid\ActionColumn',
        'template' => '{add}',
        'buttons' => [
            'add' => function ($url, $resource) use ($model) {
                if (Yii::$app->user->can('updateSchieramento')) {
                    $url = ['schieramento/add-mezzo', 'id' => $model->id, 'id_mezzo' => $resource->id];
                    return Html::a('<span class="fa fa-link"></span>&nbsp;&nbsp;', '', [
                        'title' => Yii::t('app', 'Aggiungi'),
                        'data-toggle' => 'tooltip',
                        'value'=>$resource->id,
                        'class' => 'modalAddConnection'
                        //'data-pjax' => 0
                    ]);
                } else {
                    return '';
                }
            }
        ],
    ]];

$array_filters = [];
if (!empty(Yii::$app->request->get('meta'))) {
    foreach (Yii::$app->request->get('meta') as $meta_key => $meta_filter) {
        if (!empty($meta_filter)) $array_filters[$meta_key] = $meta_filter;
    }
}



$cols = array_merge($cols, [
    'targa',
    [
        'label' => 'Data immatricolazione',
        'attribute' => 'data_immatricolazione',
        'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
        'filterType' => GridView::FILTER_DATE,
        'filterWidgetOptions' => [
            'type' => 1,
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'autoclose' => true,
                'todayHighlight' => true,
            ]
        ],
        'visible' => Yii::$app->FilteredActions->type == 'comunale' ? false : true
    ],
    [
        'label' => 'Modello',
        'attribute' => 'modello',
        'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
        'format' => 'raw',
        'value' => function ($data) {
            return '<span style="max-width: 200px; display: block; white-space: pre-wrap;">' . $data['modello'] . '</span>';
        }
    ],
    //'classe',
    //'sottoclasse',            
    [
        'label' => 'Tipo',
        'attribute' => 'idtipo',
        'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
        'filter' => Html::activeDropDownList($searchModel, 'idtipo', ArrayHelper::map(UtlAutomezzoTipo::find()->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control', 'prompt' => 'Tutti']),
        'value' => function ($data) {
            return $data['tipo']['descrizione'];
        }
    ],
    [
        'label' => 'Org.',
        'attribute' => 'org',
        'format' => 'raw',
        'width' => '500px',
        'contentOptions' => ['style' => 'width:300px; white-space: normal;'],
        'value' => function ($data) {
            return isset($data['organizzazione']) ? '<span style="max-width: 200px; display: block; white-space: pre-wrap;">' . $data['organizzazione']['denominazione'] . '</span>' : '<span style="max-width: 200px; display: block; white-space: pre-wrap;">Senza nome</span>';
        }
    ]
]);

$meta_to_show = TblTipoRisorsaMeta::find()->where(['show_in_column' => 1])->all();
foreach ($meta_to_show as $meta) {
    $cols[] =
        [
            'label' => $meta->label,
            'attribute' => '_meta',
            'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
            'filter' => Html::textInput(
                'meta[' . $meta->key . ']',
                @$array_filters[$meta->key],
                [
                    'class' => 'form-control',
                ]
            ),
            'format' => 'raw',
            'value' => function ($model) use ($meta) {
                try {
                    return $model->meta[$meta->key];
                } catch (\Exception $e) {
                    return null;
                }
            }
        ];
}


?>
<div class="mezzi-list">

   
	<?= GridView::widget([
    	'id'=>'avaible-mezzi-list',
        'dataProvider' => $dataProvider,
        'export' => false,
        'exportConfig' => ['csv' => true, 'xls' => true, 'pdf' => true],
        'filterModel' => $searchModel,
        'perfectScrollbar' => false,
        'perfectScrollbarOptions' => [],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'enablePushState' => false
            ]
        ],
        'panel' => [
            'heading' => '<h2 class="panel-title">Lista mezzi</h2>',
            'before' => $add_js.$err
        ],
        'columns' => $cols
    ]); 

    
    ?>

</div>

<?php


        Modal::begin([
            'id' => 'modal-add-connection-mezzo',
            'header' => "Conferma connessione",
            'size' => 'modal-lg',
            'options' => [
                'style' => 'z-index: 999999999999;'
            ],
            'closeButton' => false
        ]);

            

                $conn_model = new ConMezzoSchieramento;
                ?>
                <p id="error_message_form" class="text-danger"></p>
                <?php

                $form = ActiveForm::begin(['id'=>'add_connection_form', 'action' =>['schieramento/add-mezzo', 'id'=>$model->id], 'method' => 'post']); ?>
                <input type="hidden" id="conmezzoschieramento-id_utl_automezzo" class="form-control" name="ConMezzoSchieramento[id_utl_automezzo]" style="display: none;" value="5848">
                <?php

                echo $form->field($conn_model, 'date_from')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Inserisci la data ...'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]])->label('Valido dal'); ?>
                
                <?php echo $form->field($conn_model, 'date_to')->widget(DatePicker::classname(), [
                'options' => ['placeholder' => 'Inserisci la data ...'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd'
                ]])->label('Valido al'); ?>

                <div class="form-group">
                    <?= Html::a('Annulla', '', ['class' =>'btn btn-danger close-modal-empty']) ?>
                    <?= Html::submitButton('Conferma', ['class' =>'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); 

            
        Modal::end();

    ?>