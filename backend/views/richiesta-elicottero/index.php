<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\RichiestaElicotteroSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Richiesta Elicotteros';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="richiesta-elicottero-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?= Html::a('Create Richiesta Elicottero', ['create'], ['class' => 'btn btn-success']) ?>
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
            'idingaggio',
            'idoperatore',
            'tipo_intervento',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
