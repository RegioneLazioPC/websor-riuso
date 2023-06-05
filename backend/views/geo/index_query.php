<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

use common\models\geo\GeoQuery;
/* @var $this yii\web\View */
/* @var $searchModel common\models\RichiestaCanadairSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Query geografiche';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="layers-index">

    
    <p>
        <?= (Yii::$app->user->can('CreateGeoQuery')) ? Html::a('Aggiungi query', ['create-query'], ['class' => 'btn btn-success']): null ?>
    </p>

    <?php 
    $group_data = [];
    $groups = "SELECT distinct \"group\" FROM geo_query;";
    $results = Yii::$app->db->createCommand($groups)->queryAll();
    foreach ($results as $gr) {
        $group_data[$gr['group']] = $gr['group'];
    }

    $template = '';
    if(Yii::$app->user->can('UpdateGeoQuery')) $template .= ' {update}';
    if(Yii::$app->user->can('DeleteGeoQuery')) $template .= ' {delete}';
    ?>
    <?php 

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'panel' => [
            'heading' => '<h2 class="panel-title">Lista query geografiche configurate</h2>'
        ],
        'columns' => [
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $template,
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'update') {
                        $url = Url::to(['/geo/update-query', 'id'=>$model->id]);
                        return $url;
                    }
                    if ($action === 'delete') {
                        $url = Url::to(['/geo/delete-query', ['id'=>$model->id]]);
                        return $url;
                    }

                }
                
            ],
            'id',
            [
                'attribute'=>'name',
                'label'=>'Nome',
                'contentOptions' => ['style'=> 'width: 150px;'],
            ],
            [
                'attribute'=>'layer',
                'label'=>'Strato',
                'contentOptions' => ['style'=> 'width: 150px;'],
            ],
            [
                'attribute'=>'group',
                'label'=>'Raggruppamento',
                'filter' => Html::activeDropDownList($searchModel, 'group', $group_data, ['class' => 'form-control', 'prompt' => 'Tutti']),
                'contentOptions' => ['style'=> 'width: 150px;'],
            ],
            [
                'attribute'=>'query_type',
                'label'=>'Tipo query',
                'contentOptions' => ['style'=> 'width: 150px;'],
                'value' => function($data) { return GeoQuery::queryTypes()[$data->query_type]; }
            ],
            [
                'attribute'=>'result_type',
                'label'=>'Tipo risultato',
                'contentOptions' => ['style'=> 'width: 150px;'],
                'filter' => Html::activeDropDownList($searchModel, 'result_type', GeoQuery::resultType(), ['class' => 'form-control', 'prompt' => 'Tutti']),
                'value' => function($data) { return GeoQuery::resultType()[$data->result_type]; }
            ],
            [
                'attribute'=>'layer_return_field',
                'label'=>'Campo layer',
                'contentOptions' => ['style'=> 'width: 150px;'],
            ],
            [
                'attribute'=>'buffer',
                'label'=>'Buffer',
                'contentOptions' => ['style'=> 'width: 150px;'],
            ],
            [
                'attribute'=>'n_geometries',
                'label'=>'Num. feature',
                'contentOptions' => ['style'=> 'width: 150px;'],
            ],
            [
                'attribute'=>'show_distance',
                'label'=>'Mostra distanza',
                'filter' => Html::activeDropDownList($searchModel, 'show_distance', [false=>'No', true=>'Si'], ['class' => 'form-control', 'prompt' => 'Tutti']),
                'contentOptions' => ['style'=> 'width: 150px;'],
                'value' => function($data) { return $data->show_distance ? "SI" : "NO"; }
            ],
            [
                'attribute'=>'result_position',
                'label'=>'Posizione risultato',
                'filter' => Html::activeDropDownList($searchModel, 'result_position', GeoQuery::positions(), ['class' => 'form-control', 'prompt' => 'Tutti']),
                'contentOptions' => ['style'=> 'width: 150px;'],
                'value' => function($data) { return GeoQuery::positions()[$data->result_position]; }
            ],
            [
                'attribute'=>'enabled',
                'label'=>'Abilitato',
                'filter' => Html::activeDropDownList($searchModel, 'enabled', [false=>'No', true=>'Si'], ['class' => 'form-control', 'prompt' => 'Tutti']),
                'contentOptions' => ['style'=> 'width: 150px;'],
                'value' => function($data) { return $data->enabled ? "SI" : "NO"; }
            ],
            [
                'attribute'=>'created_at',
                'label'=>'Creazione',
                'format'=>'datetime'
            ]
        ],
    ]); ?>
</div>
