<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ConVolontarioIngaggioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Con Volontario Ingaggios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="con-volontario-ingaggio-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?= Html::a('Create Con Volontario Ingaggio', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_volontario',
            'id_ingaggio',
            'refund:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
