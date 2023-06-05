<?php 
use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

use yii\bootstrap\Modal;
use common\models\ConVolontarioIngaggio;

use yii\widgets\Pjax;
?>
<hr style="border-width: 3px; border-color: #d6d6d6;" />
<?php 
    if(Yii::$app->user->can('updateIngaggio')) :
        echo Html::button(
        '<i class="glyphicon glyphicon-plus"></i> Aggiungi volontario',
        [
            'title' => Yii::t('app', 'Aggiungi volontario'),
            'class' => 'popupIngaggiModal btn btn-success m20h',
            'onclick' => 'javascript:function(e){ e.preventDefault(); jQuery(\'#modal-volontario\').modal(\'show\')}'
        ]);
    endif;
?>

<?php 
$js = "

window.reload_volontari = function(){
    $.pjax.reload({ container:'#lista-volontari-container', timeout:20000 })
}
$(document).on('click', '.popupIngaggiModal', function(e) { 
    e.preventDefault();
    $('#modal-volontario').modal('show');
});
";
$this->registerJs($js, $this::POS_READY);
?>
<?php 

Pjax::begin();
?>
<?= GridView::widget([
    'id' => 'lista-volontari',
    'dataProvider' => $dataProvider,
    'pjax'=>true,
    'perfectScrollbar' => true,
    'perfectScrollbarOptions' => [],
    'panel' => [
            'heading'=>'<h2 class="panel-title">Lista volontari</h2>',
        ],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => 'Volontario',
            'attribute' => 'volontario.anagrafica.nome',
            'value' => function($data){
                if(!empty($data['volontario']) && !empty($data['volontario']['anagrafica'])){
                    return $data['volontario']['anagrafica']['nome'] . " " .$data['volontario']['anagrafica']['cognome'];
                }
            }
        ],
        [
            'label' => 'C.F.',
            'attribute' => 'volontario.anagrafica.codfiscale'
        ],
        [
            'label' => 'Rimborso',
            'attribute' => 'refund',
            'value' => function($data){
                return ($data['refund']) ? "Si" : "No";
            }
        ],
        [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        $str = ($model['refund']) ? "No" : "Si";
                        if(Yii::$app->user->can('updateIngaggio')){
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', 
                                [
                                    'ingaggio/update-volontario', 
                                    'id_ingaggio_volontario' => $model->id
                                ], [
                                'data' => [
                                    'confirm' => Yii::t('app', 'Sicuro di voler procedere?'),
                                ],
                                'title' => Yii::t('app', 'Imposta rimborso ' . $str),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'delete' => function ($url, $model) {
                        if(Yii::$app->user->can('updateIngaggio')){
                            return Html::a('<span class="fa fa-close"></span>&nbsp;&nbsp;', [
                                    'ingaggio/delete-volontario', 
                                    'id_ingaggio_volontario' => $model->id
                                ], [
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Sicuro di voler eliminare il volontario?'),
                                    ],
                                    'title' => Yii::t('app', 'Elimina'),
                                    'data-toggle'=>'tooltip'
                                ]);
                        } else {
                            return '';
                        }
                    }
                ],
            ],
    ],
]); ?>

<?php 
Pjax::end();
?>

<?php

if(Yii::$app->user->can('updateIngaggio')) :
    Modal::begin([
        'id' => 'modal-volontario',
        'header' => '<h2>AGGIUNGI VOLONTARIO</h2>',
        'size' => 'modal-lg',
        'options' => [
            'tabindex' => false
        ]
    ]);

    $add_model = new ConVolontarioIngaggio();
    $add_model->id_ingaggio = $model->id;

    echo Yii::$app->controller->renderPartial('_form_add_volontario', 
        [
            'model'=> $add_model,
            'organizzazione' => $model->idorganizzazione,
            'sede' => $model->idsede
        ]);
    Modal::end();
endif;
?>