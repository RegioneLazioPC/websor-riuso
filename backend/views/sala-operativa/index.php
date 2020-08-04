<?php

use kartik\grid\GridView;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlSalaOperativaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lista Sale Operative Regionali';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-sala-operativa-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'responsive'=>true,
        'hover'=>true,
        'panel' => [
            'heading'=>'<h2 class="panel-title"><i class="fa fa-building"></i> '.Html::encode($this->title).'</h2>',
            'before'=> Html::a('Nuova Sala Operativa', ['create'], ['class' => 'btn btn-success']),
        ],
        'columns' => [
            
            'nome',
            'indirizzo',
            'comune',
            'tipo',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
