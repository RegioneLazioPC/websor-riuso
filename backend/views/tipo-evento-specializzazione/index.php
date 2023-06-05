<?php

use yii\helpers\Html;
use kartik\grid\GridView;

use yii\helpers\ArrayHelper;
use common\models\UtlTipologia;
use common\models\TblSezioneSpecialistica;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlTipoEventoSpecializzazioneTipologieSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipo evento/specializzazione';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-aggregatore-tipologie-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('createTipoEventoSpecializzazione')) echo Html::a('Crea connessione', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [   'label' => 'Tipologia',
                'attribute' => 'id_utl_tipologia',
                //'filter'=>ArrayHelper::map(UtlTipologia::find()->asArray()->all(), 'id', 'tipologia'),
                'filter'=> Html::activeDropDownList($searchModel, 'id_utl_tipologia', ArrayHelper::map(UtlTipologia::find()->where('idparent is null')->asArray()->all(), 'id', 'tipologia'), 
                    ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data){
                    return ($data->tipologia) ? $data->tipologia->tipologia : "";
                }
            ],
            [   'label' => 'Specializzazione',
                'attribute' => 'id_tbl_sezione_specialistica',
                //'filter'=>ArrayHelper::map(UtlTipologia::find()->asArray()->all(), 'id', 'tipologia'),
                'filter'=> Html::activeDropDownList($searchModel, 'id_tbl_sezione_specialistica', ArrayHelper::map(TblSezioneSpecialistica::find()->asArray()->all(), 'id', 'descrizione'), 
                    ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data){
                    return ($data->specializzazione) ? $data->specializzazione->descrizione : "";
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => (Yii::$app->user->can('deleteTipoEventoSpecializzazione')) ? '{view} {update} {delete}' : '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('viewTipoEventoSpecializzazione')){
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio aggregatore'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateTipoEventoSpecializzazione')){
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica aggregatore'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    }
                ]
            ],
        ],
    ]); ?>
</div>
