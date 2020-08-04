<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\NotFoundHttpException;
/* @var $this yii\web\View */
/* @var $searchModel common\models\VolSedeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sedi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-sede-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('createSede')) echo Html::a('Aggiungi sede', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'id_organizzazione',
            'indirizzo',
            'comune',
            'tipo',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
