<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlIngaggioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ingaggi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-ingaggio-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
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
