<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlSquadraOperativaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Squadre Operative';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="squadra-operativa-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?= Html::a('Crea Squadra Operativa', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'columns' => [
            'id',
            'nome',
            'caposquadra',
            'comune.comune',
            'numero_membri',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
