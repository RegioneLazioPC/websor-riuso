<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlIngaggioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ingaggi';
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
            'id',
            'idevento',
            'idorganizzazione',
            'idsede',
            'idautomezzo',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
