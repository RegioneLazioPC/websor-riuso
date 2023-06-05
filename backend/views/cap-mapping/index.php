<?php

use yii\helpers\Html;
use kartik\grid\GridView;

use common\models\UtlTipologia;

use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlCategoriaAutomezzoAttrezzaturaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mappatura';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mappatura-cap-index">

    <h1><?= Html::encode( $this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <p>
        <?php if(Yii::$app->user->can('editCapMap')) echo Html::a('Crea nuova mappatura', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'columns' => [
            'id',
            'stringa_tipo_evento',
            'eventType.tipologia',
            'eventSubType.tipologia',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}'
            ],
        ],
    ]); ?>
</div>
