<?php
use common\models\EvtEvento;
use common\models\ConOperatoreEvento;
use common\models\ConOperatoreTask;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;


use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */

$query = ConOperatoreTask::find()->with('operatore', 'task', 'funzioneSupporto')->where(['idevento' => $model->id]);
$dataProvider = new ActiveDataProvider([
    'query' => $query
]);
$operatori = ConOperatoreEvento::find()->with('operatore')->where(['idevento' => $model->id])->all();

?>
<div class="utl-task-gridview">

    <div class="row">
    	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-12">
            <?php
                if(!empty($operatori)){
                    $items = [];
                    foreach ($operatori as $item) {
                        $items[] = $item->operatore->anagrafica->nome . ' ' . $item->operatore->anagrafica->cognome;
                    }
                    echo "<h3>Evento attualmente in carico a: " . implode(', ', $items) . "</h3>";
                }else{
                    echo "<h3>Evento da assegnare</h3>";
                }
            ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'format' => 'raw',
                        'attribute' => 'dataora',
                        'contentOptions' => ['style'=>'max-width: 80px; white-space: normal; overflow: auto; word-wrap: break-word;'],
                        'value' => function($model){
                            return Yii::$app->formatter->asDatetime($model->dataora);
                        }
                    ],
                    [
                        'attribute' => 'operatore',
                        'label' => 'Operatore',
                        'contentOptions' => ['style'=>'max-width: 100px; white-space: normal; overflow: auto; word-wrap: break-word;'],
                        'value' => function($model){
                            return $model->operatore->nome . " " . $model->operatore->cognome;
                        }
                    ],
                    [
                        'class' => 'kartik\grid\DataColumn',
                        'attribute' => 'funzioneSupporto.descrizione',
                        'label' => 'Funzione di supporto',
                        'contentOptions' => ['style'=>'max-width: 150px; white-space: normal; overflow: auto; word-wrap: break-word;']
                    ],
                    [
                        'class' => 'kartik\grid\DataColumn',
                        'attribute' => 'task.descrizione',
                        'label' => 'Attività operativa',
                        'contentOptions' => ['style'=>'max-width: 150px; white-space: normal; overflow: auto; word-wrap: break-word;']
                    ],
                    [
                        'class' => 'kartik\grid\DataColumn',
                        'attribute' => 'squadra.nome',
                        'label' => 'Squadre',
                        'contentOptions' => ['style'=>'max-width: 150px; white-space: normal; overflow: auto; word-wrap: break-word;']
                    ],
                    [
                        'class' => 'kartik\grid\DataColumn',
                        'attribute' => 'automezzo.classe',
                        'label' => 'Automezzi',
                        'contentOptions' => ['style'=>'max-width: 150px; white-space: normal; overflow: auto; word-wrap: break-word;']
                    ],
                    [
                        'class' => 'kartik\grid\DataColumn',
                        'attribute' => 'note',
                        'contentOptions' => ['style'=>'max-width: 150px; white-space: normal; overflow: auto; word-wrap: break-word;']
                    ],
                ],
            ]); ?>


    	</div>

    </div>



</div>
