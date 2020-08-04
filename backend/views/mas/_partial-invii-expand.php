<?php
use common\models\UtlEvento;
use common\models\MasSingleSend;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
//use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */

$query = $model->getMasSingleSends();
$dataProvider = new ActiveDataProvider([
    'query' => $query
]);

?>
<div class="utl-task-gridview">

    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-12">

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'toggleData'=>false,
                'export' => false,
                'panel' => [
                    'heading'=>'<h2 class="panel-title">'.Html::encode('Lista tentativi di invio').'</h2>',

                ],
                'columns' => [
                    [
                    	'attribute'=>'status',
                    	'label'=>'Stato',
                    	'value'=> function($model) {
                    		return $model->getStato();
                    	}
                    ],
                    [
                    	'attribute'=>'sent_time',
                    	'label'=>'Data invio',
                    	'format'=>'datetime'
                    ],
                    [
                        'attribute'=>'feedback_time',
                        'label'=>'Data feedback',
                        'format'=>'datetime'
                    ]
                ],
            ]); ?>

        </div>

    </div>

</div>
