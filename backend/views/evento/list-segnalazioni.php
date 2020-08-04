<?php

use common\models\LocProvincia;
use common\models\UtlSegnalazione;
use common\models\UtlTipologia;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlSegnalazioneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista Segnalazioni associate all\'evento N. Protocollo '.$evento->num_protocollo;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-segnalazione-index">


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive'=>true,
        'hover'=>true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'panel' => [
            'heading'=>'<h2 class="panel-title"><i class="fa fa-warning"></i> '.Html::encode($this->title).'</h2>',
            'before'=> Html::a('<i class="fa fa-refresh p5w"></i> Annulla', ['index'], ['class' => 'btn btn-default']) .  Html::a('<i class="fa fa-plus p5w"></i> Aggiungi segnalazione', ['#'], ['class'=>'btn btn-info m5w', 'data-toggle' => 'modal', 'data-target' => "#modal-segnalazione-create"])
        ],
        'columns' => [
            [
                'attribute' => 'num_protocollo',
                'label' => 'N. Prot.',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'label' => 'Segnalatore',
                'attribute' => 'utente',
                'format' => 'raw',
                'filter'=> Html::activeDropDownList($searchModel, 'utente', array(1=>'Cittadino privato', 2=>'Ente Pubblico'), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data){
                    if(!empty($data->utente->tipo)){
                        $tipoSegnalatore = ($data->utente->tipo == 1) ? 'Cittadino privato' : 'Ente Pubblico';
                        return $tipoSegnalatore;
                    }
                }
            ],
            [
                'label' => 'Dati segnalante',
                'attribute' => 'utente',
                'format' => 'raw',
                'value' => function($data){
                    if(!empty($data->utente->nome)){
                        return sprintf('<i class="fa fa-user p5w"></i>%s %s <br> <i class="fa fa-phone p5w"></i>  %s', 
                            Html::encode($data->utente->nome), 
                            Html::encode($data->utente->cognome), 
                            Html::encode($data->utente->telefono)
                        );
                    }
                }
            ],
            [   'label' => 'Tipologia Evento',
                'attribute' => 'tipologia_evento',
                'contentOptions' => ['style'=>'width: 180px;'],
                'filter'=> Html::activeDropDownList($searchModel, 'tipologia_evento', ArrayHelper::map(UtlTipologia::find()->asArray()->all(), 'id', 'tipologia'), ['class' => 'form-control','prompt' => 'Tutte le tipologie']),
                'value' => function($data){
                    if(!empty($data->tipologia->tipologia)){
                        return $data->tipologia->tipologia;
                    }
                }
            ],
            [
                'label' => 'Comune',
                'attribute' => 'comune.comune',
                'value' => function($data){
                    if(!empty($data['comune'])){
                        return $data['comune']['comune'];
                    }
                }
            ],
            [
                'label' => 'Provincia',
                'attribute' => 'comune.provincia',
                'filter'=> Html::activeDropDownList($searchModel, 'comune.provincia', ArrayHelper::map(LocProvincia::find()->where(['id_regione' => 18])->all(), 'id', 'sigla'), ['class' => 'form-control','prompt' => 'Tutte']),
                'value' => function($data){
                    if(!empty($data['comune'])){
                        return $data['comune']['provincia']['provincia'].' ('.$data['comune']['provincia_sigla'].')';
                    }
                }
            ],

            [
                'attribute' => 'indirizzo',
                'contentOptions' => ['style' => 'width:50px;'],
            ],
            [
                'attribute' => 'note',
                'format' => 'raw',
                'contentOptions' => ['style'=>'max-width: 200px; white-space: normal; word-wrap: break-word;'],
                'value' => function($data){
                    return sprintf('<a href="#" data-toggle="tooltip" title="%s"><i class="fa fa-info-circle"></i> Dettaglio </a>', 
                        Html::encode(str_replace("\"", "", $data->note)) );
                }
            ],
            [   'label' => 'Data',
                'attribute' => 'dataora_segnalazione',
                'value' => function($data){
                    return Yii::$app->formatter->asDateTime($data->dataora_segnalazione);
                },
                'contentOptions' => ['style' => 'width:50px;'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('manageEvento')){
                            return Html::a(
                                '<span class="fa fa-eye"></span>&nbsp;&nbsp;',
                                ['view-segnalazione','id'=>$model->id],
                                [
                                    'title' => Yii::t('app', 'Dettaglio evento'),
                                    'class' => 'popupModal'
                                ]
                            ) ;
                        }else{
                            return '';
                        }
                    },
                ]
            ],
        ],
    ]); ?>
</div>

<?php
Modal::begin([
    'id' => 'myModal',
    'header' => '<h2>DETTAGLIO SEGNALAZIONE</h2>',
]);

Modal::end();
?>


<?php

Modal::begin([
    'id' => 'modal-segnalazione-create',
    'header' => '<h2>Nuova segnalazione evento Prot.  '. Html::encode($evento->num_protocollo) .'</h2>',
    'size' => Modal::SIZE_LARGE,
]);

echo Yii::$app->controller->renderPartial('modal-segnalazione-create', ['evento'=> $evento]);

Modal::end();

?>

<?php
$this->registerJs("$(function() {
   $('.popupModal').click(function(e) {
     e.preventDefault();
     $('#myModal').modal('show').find('.modal-body')
     .load($(this).attr('href'));
   });
});");
?>


