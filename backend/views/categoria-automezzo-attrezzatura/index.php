<?php

use yii\helpers\Html;
use kartik\grid\GridView;

use common\models\UtlTipologia;

use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlCategoriaAutomezzoAttrezzaturaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categorie automezzi/attrezzature';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-categoria-automezzo-attrezzatura-index">

    <h1><?= Html::encode( $this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('createCategoria')) echo Html::a('Crea categoria', ['create'], ['class' => 'btn btn-success']) ?>
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
            'descrizione',
            [   'label' => 'Tipo evento',
                'attribute' => 'id_tipo_evento',
                'filter'=> Html::activeDropDownList($searchModel, 'id_tipo_evento', ArrayHelper::map(UtlTipologia::find()->where('idparent is null')->asArray()->all(), 'id', 'tipologia'), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data){
                    return ($data->tipoEvento) ? $data->tipoEvento->tipologia : "";
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => (Yii::$app->user->can('deleteCategoria')) ? '{view} {update} {delete}' : '{view} {update}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        if(Yii::$app->user->can('viewCategoria')){
                            return Html::a('<span class="fa fa-eye"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Dettaglio categoria'),
                                'data-toggle'=>'tooltip'
                            ]) ;
                        }else{
                            return '';
                        }
                    },
                    'update' => function ($url, $model) {
                        if(Yii::$app->user->can('updateCategoria')){
                            return Html::a('<span class="fa fa-pencil"></span>&nbsp;&nbsp;', $url, [
                                'title' => Yii::t('app', 'Modifica categoria'),
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
