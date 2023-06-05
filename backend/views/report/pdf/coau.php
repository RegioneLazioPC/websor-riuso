<?php 
use common\models\UtlTipologia;

require_once '_header.php';


$cols = [
            [
                'attribute' => 'regione',
                'label' => 'Regione',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;'],
            ],
            [
                'attribute' => 'provincia',
                'label' => 'Provincia',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'num_boschivo',
                'label' => 'Incendi boschivi',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'solo_regionali',
                'label' => 'Incendi boschivi con soli aeromobili regionali',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'num_boschivo_chiuso',
                'label' => 'Incendi boschivi spenti',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'num_boschivo_aperto',
                'label' => 'Incendi boschivi in atto',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 60px;']
            ],
            [
                'attribute' => 'num_non_boschivo',
                'label' => 'Incendi non boschivi',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 60px;']
            ]            
        ];

require_once '_data.php';
?>