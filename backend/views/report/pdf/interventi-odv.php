<?php 
use common\models\UtlTipologia;

require_once '_header.php';


$cols = [
            [
                'attribute' => 'num_elenco_territoriale',
                'label' => 'Numero elenco territoriale',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'denominazione',
                'label' => 'Organizzazione',
                'width' => 200,
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:200px;'],
                'contentOptions' => ['style'=>'width: 200px;white-space: normal;']
            ],
            [
                'attribute' => 'totale',
                'label' => "Totale",
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'chiuso',
                'label' => "Chiuso",
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'rifiutato',
                'label' => "Totale rifiutato",
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'in_attesa',
                'label' => "In attesa di conferma",
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'confermato',
                'label' => "Confermato",
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'fuori_orario',
                'label' => "RIFIUTATO - FUORI ORARIO",
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'non_risponde',
                'label' => "RIFIUTATO - NON RISPONDE",
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'mezzo_non_disponibile',
                'label' => "RIFIUTATO - MEZZO NON DISPONIBILE",
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'squadra_non_disponibile',
                'label' => "RIFIUTATO - SQUADRA NON DISPONIBILE",
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ],
            [
                'attribute' => 'altro',
                'label' => "RIFIUTATO - ALTRO",
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;']
            ]
        ];

require_once '_data.php';
?>