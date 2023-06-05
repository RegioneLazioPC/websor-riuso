<?php 
use common\models\UtlTipologia;

require_once '_header.php';


$eli_cols = [];
        usort( $elicotteri, function($a, $b){
            return (str_replace(" ", "", $a) > str_replace(" ", "", $b) ) ? 1 : -1;
        });
        foreach ($elicotteri as $key => $value) {
            $eli_cols[] = [
                'attribute' => 'device_'.$key,
                'label' => isset($value['device_name']) ? $value['device_name'] : ' - ',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 120px;'],
                'value' => function($model) use ($key) {
                    return (isset($model['device_'.$key])) ? $model['device_'.$key] : "";
                }
            ];
        }

        
        
        $cols = [
            [
                'attribute' => 'anno',
                'label' => 'Anno',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'mese',
                'label' => 'Mese',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 60px;'],
                'value' => function($model){
                    return !empty($model['mese']) ? $model['mese'] : "";
                }
            ],
            [
                'attribute' => 'giorno',
                'label' => 'Data',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 60px;'],
                'value' => function($model){
                    if(!empty($model['giorno']) && !empty($model['mese'])) {
                        return $model['giorno'] . "/" . $model['mese'] . "/" . $model['anno'];
                    } else {
                        return "";
                    }
                }
            ],  
        ];

        $cols = array_merge($cols, $eli_cols);

require_once '_data.php';
?>