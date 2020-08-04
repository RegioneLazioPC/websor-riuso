<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\RichiestaCanadairSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Richiesta Canadairs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="richiesta-canadair-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?= Html::a('Create Richiesta Canadair', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'idevento',
            'idoperatore',
            'idcomunicazione',
            'created_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
