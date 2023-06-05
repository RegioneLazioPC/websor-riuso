<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlIngaggioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Configurazioni applicativo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-ingaggio-index">

    <?= GridView::widget([
        'dataProvider' => $provider,
        'filterModel' => null,
        'panel' => [
            'heading'=>'<h2 class="panel-title"><i class="glyphicon glyphicon-cog"></i> '.Html::encode($this->title).'</h2>',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'key',
            'label',
            [
                'attribute'=>'description',
                'contentOptions' => ['style'=>'max-width: 200px; white-space: unset;'],
            ],
            [
                'attribute' => 'value',
                'label' => 'Valore',
                'format' => 'raw',
                'contentOptions' => ['style'=>'max-width: 200px; white-space: unset; overflow-x: auto;'],
                'value' => function($data) {
                    if(!empty($data['value'])){
                        $list = '';

                        foreach ($data['value'] as $key => $value) {
                            $list .= '<li><b>'.$key.':</b> '.$value.'</li>';
                        }

                        return '<ul class="list-unstyled">'.$list.'</ul>';
                    }

                    return '';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('Admin') && $model['editable']){
                            return Html::a(
                                '<span class="fa fa-pencil"></span>&nbsp;&nbsp;', ['config/update', 'key' => $model['key']], [
                                'title' => Yii::t('app', 'Modifica valore'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'delete' => function ($url, $model) {
                        if(Yii::$app->user->can('Admin') && $model['value'] && $model['value'] != ''){
                            return Html::a(
                                '<span class="fa fa-trash"></span>&nbsp;&nbsp;', 
                                ['config/delete', 'key' => $model['key']], 
                                [
                                    'title' => 'Elimina valore',
                                    'data' => [
                                        'confirm' => 'Sicuro di voler rimuovere il valore di questa chiave?',
                                        'method' => 'post'
                                    ]
                                ]) ;
                        } else {
                            return '';
                        }
                    }
                ]
            ],
        ],
    ]); ?>
</div>
