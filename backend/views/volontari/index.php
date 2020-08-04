<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\VolVolontarioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Volontari';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-volontario-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('createVolontario')) echo Html::a('Aggiungi volontario', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'label' => 'Nome',
                'attribute' => 'anagrafica.nome',
                'value' => function($data){
                    if(!empty($data['anagrafica'])){
                        return $data['anagrafica']['nome'];
                    }
                }
            ],
            [
                'label' => 'Cognome',
                'attribute' => 'anagrafica.cognome',
                'value' => function($data){
                    if(!empty($data['anagrafica'])){
                        return $data['anagrafica']['cognome'];
                    }
                }
            ],
            [
                'label' => 'COD.FISC.',
                'attribute' => 'anagrafica.codfiscale',
                'value' => function($data){
                    if(!empty($data['anagrafica'])){
                        return $data['anagrafica']['codfiscale'];
                    }
                }
            ],
            [
                'label' => 'Operativo',
                'attribute' => 'operativo',
                'value' => function($data){
                    return ($data['operativo']) ? 'Si' : 'No';
                }
            ],
            'ruolo',
            'spec_principale',
            [   
                'label' => 'Valido dal',
                'attribute' => 'valido_dal',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'type' => 1,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoclose' => true,
                        'todayHighlight' => true,
                    ]
                ]
            ],
            'organizzazione.denominazione',
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}',                
            ],
        ],
    ]); ?>
</div>
