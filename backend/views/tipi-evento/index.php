<?php

use yii\helpers\Html;
use kartik\grid\GridView;

use yii\helpers\ArrayHelper;
use common\models\UtlTipologia;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlTipologiaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipi evento';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-tipologia-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('createTipoEvento')) echo Html::a('Crea tipo evento', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'tipologia',
            [
                'label'=>'Categoria messaggio cap',
                'attribute'=>'cap_category',
                'filter'=> Html::activeDropDownList($searchModel, 'cap_category', \common\models\cap\CapExposedMessage::getDropdownCategories(), ['class' => 'form-control','prompt' => 'Tutti']),
            ],
            [   'label' => 'Genitore',
                'attribute' => 'idparent',
                'filter'=> Html::activeDropDownList($searchModel, 'idparent', ArrayHelper::map(UtlTipologia::find()->where('idparent is null')->asArray()->all(), 'id', 'tipologia'), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data){
                    return ($data->tipologiaGenitore) ? 
                    $data->tipologiaGenitore->tipologia : "";
                }
            ],
            [   'label' => 'In app',
                'attribute' => 'check_app',
                'value' => function($data){
                    if(!empty($data->id_parent)) return "-";
                    
                    return ($data->check_app == 1) ? 
                    "Si" : "No";
                }
            ],
            [   'label' => 'Icona',
                'attribute' => 'icon_name',
                'value' => function($data){
                    return ($data->idparent) ? 
                    " - " : $data->icon_name;
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => (Yii::$app->user->can('deleteTipoEvento')) ? '{view} {update} {delete}' : '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('viewTipoEvento')){
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio tipo evento'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateTipoEvento')){
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica tipo evento'),
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
