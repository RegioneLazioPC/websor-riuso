<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\NotFoundHttpException;
/* @var $this yii\web\View */
/* @var $searchModel common\models\VolSedeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ERRORI SYNC';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-sede-index">

    <h1><?= Html::encode($this->title) ?></h1>
    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'columns' => [
            
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute'=>'level',
                'filter' => Html::activeDropDownList($searchModel, 'level', 
                    ['WARNING' => 'WARNING', 'FATAL' => 'FATAL']
                    , ['class' => 'form-control', 'prompt' => 'Tutti']),
                
            ],
            [
                'attribute'=>'service',
                'filter' => Html::activeDropDownList($searchModel, 'service', 
                    ['odv' => 'ODV', 'login' => 'LOGIN', 'volontario' => 'VOLONTARIO', 'risorsa'=>'RISORSA']
                    , ['class' => 'form-control', 'prompt' => 'Tutti']),
                
            ],
            [
                'label' => 'Data creazione (filtro < di)',
                'attribute' => 'created_at',
                'format' => 'datetime',
                'filterType' => GridView::FILTER_DATETIME,
                'filterWidgetOptions' => [
                    'type' => 1,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd HH:mm',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ]
            ]
        ],
    ]); ?>
</div>
