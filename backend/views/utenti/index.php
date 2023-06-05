<?php

use kartik\grid\GridView;
use yii\helpers\Html;

use common\models\UtlUtente;
use common\models\UtlRuoloSegnalatore;
use common\models\TblSezioneSpecialistica;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlUtenteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista utenti APP';
$this->params['breadcrumbs'][] = $this->title;


$str = '';
if( Yii::$app->user->can('updateAppUser') ) {
    $str .= '<form style="display: inline-block;" method="POST" action="'.Yii::$app->request->url.'"><input type="hidden" name="action" value="abilitate" />
                    <input type="hidden" name="'. Yii::$app->request->csrfParam .'" value="'. Yii::$app->request->csrfToken .'" />
                    <button type="submit" style="margin-left: 16px" class="btn btn-warning">Abilita i risultati filtrati</button>
                </form>' . 
                '<form style="display: inline-block;" method="POST" action="'.Yii::$app->request->url.'"><input type="hidden" name="action" value="disabilitate" />
                    <input type="hidden" name="'. Yii::$app->request->csrfParam .'" value="'. Yii::$app->request->csrfToken .'" />
                    <button type="submit" style="margin-left: 16px" class="btn btn-danger">Disabilita i risultati filtrati</button>
                </form>';
}


?>
<div class="utl-utente-index">


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive'=>true,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'hover'=>true,
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'panel' => [
            'heading'=>'<h2 class="panel-title"><i class="fa fa-users"></i> '.Html::encode($this->title).'</h2>',
            'before'=> (Yii::$app->user->can('createAppUser')) ? '<div>' . 
            Html::a('Nuovo Utente', ['create'], ['class' => 'btn btn-success']) . 
                $str . 
            '</div>' : '',
        ],
        'pager' => [
            'firstPageLabel' => 'Pagina iniziale',
            'lastPageLabel'  => 'Pagina finale'
        ],
        'columns' => [
            [
                'label' => 'Username',
                'attribute' => 'username',
                'value' => 'user.username',
                'contentOptions'=>['style'=>'max-width: 100px; overflow: auto; word-wrap: break-word;']
            ],
            [
                'label' => 'Nome',
                'attribute' => 'nome',
                'value' => function($data){
                    return $data['anagrafica']['nome'];
                 }
            ],
            [
                'label' => 'Cognome',
                'attribute' => 'cognome',
                'value' => function($data){
                    return $data['anagrafica']['cognome'];
                 }
            ],
            [
                'label' => 'Data registrazione',
                'attribute' => 'created_at',
                'format' => 'raw',
                'contentOptions'=>['style'=>'width: 200px; overflow: auto; word-wrap: break-word;'],
                'value' => function($data){
                    return isset($data['user']) && isset($data['user']['created_at']) && !empty($data['user']['created_at']) ? Yii::$app->formatter->asDate($data['user']['created_at']).' '.Yii::$app->formatter->asTime($data['user']['created_at']) : "-";
                }
            ],/*
            [
                'label' => 'Telefono',
                'attribute' => 'telefono',
                'value' => function($data){
                    return $data['anagrafica']['telefono'];
                 }
            ],*/
            //'email:email',
            /*
            [
                'label' => 'Email',
                'attribute' => 'email',
                'format' => 'html',
                'contentOptions'=>['style'=>'max-width: 100px; overflow: auto; word-wrap: break-word;'],
                'value' => function($data){
                    return Yii::$app->formatter->asEmail($data['anagrafica']['email']);
                 }
            ],*/
            [
                'label' => 'Tipo utente',
                'attribute' => 'tipo',
                'format' => 'raw',
                'filter'=> Html::activeDropDownList($searchModel, 'tipo', UtlUtente::getTipi(), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($model){
                    return $model->getTipo();
                }
            ],
            /*
            [
                'label' => 'Ruolo utente',
                'attribute' => 'id_ruolo_segnalatore',
                'format' => 'raw',
                //'filter' => array(1=>'Cittadino privato', 2=>'Ente Pubblico'),
                'filter'=> Html::activeDropDownList($searchModel, 'id_ruolo_segnalatore', ArrayHelper::map(UtlRuoloSegnalatore::find()->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data){
                    return $data['ruoloSegnalatore']['descrizione'];
                }
            ],*/
            [
                'label' => 'Organizzazione',
                'attribute' => 'id_organizzazione',
                'value' => function($model){
                    if(!empty($model->organizzazione)) {
                        return implode("; ", array_map(function($org){ 
                            return $org->ref_id; 
                        }, $model->organizzazione));
                    } else {
                        return "-";
                    }
                }
            ],
            [
                'label' => 'Nome organizzazione',
                'attribute' => 'nome_organizzazione',
                'format' => 'raw',
                'contentOptions'=>['style'=>'width: 300px; overflow: auto; word-wrap: break-word;'],
                'value' => function($model){
                    if(!empty($model->organizzazione)) {
                        return implode("; ", array_map(function($org){ 
                            return $org->denominazione; 
                        }, $model->organizzazione));
                    } else {
                        return "-";
                    }
                }
            ],
            [
                'attribute'=>'specializzazione',
                'label' => 'Specializzazione',
                'contentOptions' => [
                    'style'=>'max-width:200px; min-height:100px; overflow: auto; word-wrap: break-word;'
                ],
                'filter'=> Html::activeDropDownList(
                    $searchModel, 
                    'specializzazione', 
                    ArrayHelper::map( TblSezioneSpecialistica::find()->all(), 'id', 'descrizione'), 
                    ['class' => 'form-control','prompt' => 'Tutti']
                ),
                'value'=>function($model) {
                    if(!empty($model->organizzazione)) {
                        $sezioni = [];
                        
                        foreach ($model->organizzazione as $org) {
                            foreach ($org->sezioneSpecialistica as $sezione) {
                                $sezioni[] = $sezione->descrizione;
                            }
                        }

                        return implode("; ", array_unique($sezioni));
                        
                    } else {
                        return '-';
                    }
                }
            ],
            /*
            [
                'label' => 'Nato a',
                'attribute' => 'luogo_nascita',
            ],*/
            [
                'label' => 'Codice',
                'attribute' => 'codice_attivazione'
            ],
            [
                'label' => 'Stato',
                'attribute' => 'enabled',
                'format' => 'raw',
                'filter' => array(0=>'Disabilitato', 1=>'Abilitato'),
                'value' => function($data){
                    return ($data->enabled == 1) ? '<i class="fa fa-circle text-success"></i> Abilitato' : '<i class="fa fa-circle text-danger"></i> Disabilitato';
                }
            ],
            [
                'label' => false,
                'format' => 'raw',
                'value' => function($data){

                    if(Yii::$app->user->can('updateAppUser')) {
                        if($data->enabled != 1){

                            return  Html::a('Abilita', ['utenti/attiva?id='.$data->id], [
                                        'class' => 'btn btn-block btn-warning btn-xs',
                                        'title' => Yii::t('app', 'Attivazione manuale'),
                                        'data-confirm' => Yii::t('yii', 'Sei sicuro di voler procedere con l\'attivazione manuale di questo utente?'),
                                        'data-method' => 'post',
                                        'data-toggle'=>'tooltip'
                                    ]);

                        }else{
                            return  Html::a('Disabilita', ['utenti/disattiva?id='.$data->id], [
                                        'class' => 'btn btn-block btn-danger btn-xs',
                                        'title' => Yii::t('app', 'Disattivazione manuale'),
                                        'data-confirm' => Yii::t('yii', 'Sei sicuro di voler procedere con la disabilitazione manuale di questo utente?'),
                                        'data-method' => 'post',
                                        'data-toggle'=>'tooltip'
                                    ]);
                        }
                    }
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('listAppUser')){
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettagli'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateAppUser') && $model->tipo == 2){
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    }
                ],
            ]
        ],
    ]); ?>
</div>
