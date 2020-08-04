<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\MasMessageTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Templates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mass-message-template-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?= Html::a('Crea nuovo template', ['create-template'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'nome',
            [
                'label' => 'Creazione',
                'attribute'=>'created_at',
                'format' => 'datetime'
            ],
            [
                'label' => 'Ultimo aggiornamento',
                'attribute'=>'updated_at',
                'format' => 'datetime'
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        
                        if(Yii::$app->user->can('listMasTemplate')){
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>&nbsp;&nbsp;', ['view-template', 'id' => $model->id], [
                                'title' => Yii::t('app', 'Dettagli'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        
                        if(Yii::$app->user->can('updateMasTemplate')){
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;', ['update-template', 'id' => $model->id], [
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
