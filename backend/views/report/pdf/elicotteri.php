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
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'giorno',
                'label' => 'Giorno',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ]
        ];

        foreach ($province as $key => $value) {
            
            foreach ($value['comuni'] as $comune) {
                $cols[] = [
                    'attribute' => 'total_comune_' . \backend\controllers\ReportController::normalize($comune),
                    'label' => $comune . " (" . $key . ")",
                    'format' => 'raw',
                    'contentOptions' => ['style'=>'width: 150px;']
                ];
            }

            $cols[] = [
                'attribute' => 'total_provincia_' . $key,
                'label' => "Totale " . $key,
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 150px;']
            ];
        }

        $cols[] = [
                'attribute' => 'total',
                'label' => 'Totale',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ];

require_once '_data.php';
?>