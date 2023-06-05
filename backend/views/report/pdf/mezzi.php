<?php 
use common\models\UtlTipologia;

require_once '_header.php';


$cols = [
            [
                'attribute' => 'anno',
                'label' => 'Anno',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'mese',
                'label' => 'Mese',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 200px;'],
                'value' => function($model) {
                    return (empty($model['mese'])) ? '' : $model['mese'];
                }
            ],
            [
                'attribute' => 'giorno',
                'label' => 'Giorno',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 200px;'],
                'value' => function($model) {
                    return (empty($model['giorno'])) ? '' : $model['giorno'];
                }
            ]
        ];

        $filter_types = [];
        foreach ($tipologie as $tipologia) {
            $filter_types[] = $tipologia;
            $cols[] = [
                'attribute' => $tipologia,
                'label' => $tipologia,
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 200px;'],
                'value' => function($model) use ($tipologia) {
                    return (empty($model[$tipologia])) ? 0 : $model[$tipologia];
                }
            ];
        }

        $cols[] = [
                'attribute' => 'totale',
                'label' => 'Totale',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ];

require_once '_data.php';
?>