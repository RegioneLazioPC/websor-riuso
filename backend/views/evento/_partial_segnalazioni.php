<?php
use common\models\LocProvincia;
use common\models\UtlTipologia;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */
/* @var $form yii\widgets\ActiveForm */
?>

<?= GridView::widget([
    'dataProvider' => $segnalazioniDataProvider,
    'filterModel' => $segnalazioniSearchModel,
    'responsive'=>true,
    'hover'=>true,
    'perfectScrollbar' => true,
    'perfectScrollbarOptions' => [],
    'panel' => [
        'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-bell"></i> '.Html::encode('Lista segnalazioni associate all\'evento').'</h3>',
    ],
    'export'=> false,
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
            'filter'=> Html::activeDropDownList($segnalazioniSearchModel, 'utente', array(1=>'Cittadino privato', 2=>'Ente Pubblico', 3 => 'Organizzazione di Volontariato'), ['class' => 'form-control','prompt' => 'Tutti']),
            'value' => function($data){
                $tel = (!empty($data->telefono_segnalatore)) ? "<i class=\"fa fa-phone\"></i> " . Html::encode($data->telefono_segnalatore) : "";

                $nome = Html::encode(@$data->nome_segnalatore . " " . @$data->cognome_segnalatore);
                $profilo = '-';
                switch(@$data->utente->tipo) {
                    case 2:
                        $profilo = 'Ente pubblico';
                    break;
                    case 3:
                        if( !empty($data->organizzazione) ) {
                            $profilo = "Organizzazione di volontariato<br />" . Html::a($data->organizzazione->denominazione, ['organizzazione-volontariato/view', 'id'=>$data->organizzazione->id], ['class' => '']);
                        } else {
                            $profilo = "Organizzazione di volontariato";
                        }
                    break;
                    case null:
                        $profilo = '-';
                    break;
                    default:
                        $profilo = 'Cittadino privato';
                    break;
                }

                return $profilo . "<br />" . $nome . "<br />" . $tel;
                
            }
        ],
        [
            'attribute' => 'fonte',
            'filter'=> Html::activeDropDownList($segnalazioniSearchModel, 'fonte', $segnalazioniSearchModel->getFonteArray(), ['class' => 'form-control','prompt' => 'Tutti']),
            'label' => 'Fonte'
        ],
        [
            'label' => 'Dati segnalante',
            'attribute' => 'utente',
            'format' => 'raw',
            'value' => function($data){
                if(!empty($data->utente) && !empty($data->utente->anagrafica) && !empty($data->utente->anagrafica->nome)) {
                    return sprintf('<i class="fa fa-user p5w"></i>%s %s <br> <i class="fa fa-phone p5w"></i>  %s', 
                        Html::encode( $data->utente->anagrafica->nome ),  
                        Html::encode( $data->utente->anagrafica->cognome ), 
                        Html::encode( $data->utente->anagrafica->telefono )
                    );
                }
            }
        ],

        [   'label' => 'Tipologia Evento',
            'attribute' => 'tipologia_evento',
            'format' => 'raw',
            'contentOptions' => ['style'=>'width: 180px;'],
            'filter'=> Html::activeDropDownList($segnalazioniSearchModel, 'tipologia_evento', ArrayHelper::map(UtlTipologia::find()->asArray()->all(), 'id', 'tipologia'), ['class' => 'form-control','prompt' => 'Tutte le tipologie']),
            'value' => function($data){
                if(!empty($data->tipologia->tipologia)){
                    return Html::encode( $data->tipologia->tipologia );
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
            'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(LocProvincia::find()
                    ->where([
                                Yii::$app->params['region_filter_operator'], 
                                'id_regione', 
                                Yii::$app->params['region_filter_id']
                            ])
                    ->all(), 'id', 'sigla'),
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'multiple' => true,
                        'allowClear'=>true,
                    ]
                ],
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
                return sprintf('<a href="#" data-toggle="tooltip" title="%s"><i class="fa fa-info-circle"></i> Dettaglio </a>', Html::encode( str_replace("\"", "", $data->note) ) );
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
                    if(Yii::$app->user->can('viewSegnalazione')){
                        return Html::a(
                            '<span class="fa fa-eye"></span>&nbsp;&nbsp;',
                            ['view-segnalazione','id'=>$model->id],
                            [
                                'title' => Yii::t('app', 'Dettaglio segnalazione'),
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


<?php
Modal::begin([
    'id' => 'myModal',
    'header' => '<h2>DETTAGLIO SEGNALAZIONE</h2>',
]);

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
