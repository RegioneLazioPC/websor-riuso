<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\RubricaGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gruppi rubrica';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rubrica-group-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('createRubricaGroup')) echo Html::a('Crea nuovo gruppo', ['create'], ['class' => 'btn btn-success']); ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute'=>'name',
                'label'=>'Nome'
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('listRubricaGroup')){
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettagli'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateRubricaGroup')){
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    }
                ],
            ],
        ],
    ]); ?>
</div>
