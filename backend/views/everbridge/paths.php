<?php
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;

use common\utils\EverbridgeUtility;
use yii\helpers\Html; 

    

    if(is_array($everbridge_data)) {
        $provider = new ArrayDataProvider([
            'allModels' => $everbridge_data,
            'pagination' => false
        ]);

        echo GridView::widget([
            'dataProvider' => $provider,
            'responsive'=>true,
            'hover'=>true,
            'export' => Yii::$app->user->can('exportData') ? [] : false,
            'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
            'panel' => [
                'heading'=>'<h2 class="panel-title">Dati everbridge</h2>',                                    
            ],
            'columns' => [
                [
                    'label' => 'Valore',
                    'attribute' => 'contact'
                ],
                [
                    'label' => 'Path',
                    'attribute' => 'path'
                ],
                [
                    'label' => 'Id esterno',
                    'attribute' => 'externalId'
                ]    
            ],
        ]); 
    } else {
        echo $everbridge_data;
    }
            
?>