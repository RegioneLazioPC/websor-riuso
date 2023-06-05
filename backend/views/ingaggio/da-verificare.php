<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlIngaggioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Feedback attivazioni da verificare';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-ingaggio-index">

    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'panel' => [
            'heading'=>'<h2 class="panel-title"><i class="glyphicon glyphicon-bell"></i> '.Html::encode($this->title).'</h2>',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Evento (filtro numerico)',
                'attribute' => 'idevento',
                'value' => function($data) {
                	return $data->evento->num_protocollo;
                }
            ],
            [
                'label' => 'Num. territoriale',
                'attribute' => 'organizzazione.ref_id',
                'contentOptions' => ['style'=>'max-width: 90px; white-space: unset;'],
            ],
            [
                'label' => 'Organizzazione',
                'contentOptions' => ['style'=>'max-width: 150px; white-space: unset;'],
                'attribute' => 'organizzazione.denominazione'
            ],
            [
                'label' => 'Sede',
                'filter'=>false,
                'attribute' => 'null',
                'value' => function($data) {
                	if(!empty($data->idsede)) {
                		return $data->sede->indirizzo . " (" . @$data->sede->locComune->comune . ")";
                	}

                	return '';
                }
            ],
            [
                'label' => 'Risorsa',
                'filter' => false,
                'contentOptions' => ['style'=>'max-width: 200px; white-space: unset;'],
                'attribute' => 'null',
                'value' => function($data) {
                	if(!empty($data->idautomezzo)) {
                		return $data->automezzo->targa . " " . $data->automezzo->tipo->descrizione;
                	}

                	if(!empty($data->idattrezzatura)) {
                		$str = $data->attrezzatura->tipo->descrizione;
                		if($data->attrezzatura->modello) $str .= ' MODELLO: ' . $data->attrezzatura->modello;
                		if($data->attrezzatura->classe) $str .= ' CLASSE: ' . $data->attrezzatura->classe;
                		if($data->attrezzatura->unita && $data->attrezzatura->unita > 1) $str .= ' N: ' . $data->attrezzatura->unita;
                		return $str;
                	}
                }
            ],
            [
                'label' => 'Data',
                'attribute' => 'feedbackRl.created_at',
                'filter'=>false,
                'format'=> 'datetime'
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('updateIngaggio')){
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio attivazione'),
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
