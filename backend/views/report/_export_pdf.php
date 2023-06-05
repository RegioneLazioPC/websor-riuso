<?php 
use yii\helpers\Html;
use yii\helpers\Url;

if(count($cols) /*<= 15*/ > 0) {
    echo Html::a('Crea report pdf', 
        array_merge(
            [Yii::$app->request->getPathInfo()], 
            Yii::$app->request->getQueryParams(), 
            //['show_cols' => implode(";", array_map(function($c){ return $c['label'].".....".$c['attribute'];}, $cols) )],
            ['format'=>'pdf']
        ), [
            'class' => 'btn btn-primary', 
            'target'=>'_blank',
            'style' => 'margin-bottom: 12px'
        ]);
} else {
    echo "<div style='margin-bottom: 12px'>
        Filtra i risultati in modo che ci siano meno di 15 colonne
    </div>";
}