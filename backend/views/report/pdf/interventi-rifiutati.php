<?php 
use common\models\UtlTipologia;
use common\models\UtlIngaggio;

require_once '_header.php';


$cols = [
            [
                'attribute' => 'num_protocollo',
                'label' => 'Protocollo websor',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 120px;']
            ],[
                'attribute' => 'created_at',
                'label' => 'Giorno',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 100px;'],
                'value'=>function($data) {
                    return Yii::$app->formatter->asDate($data['created_at']);
                }
            ],[
                'attribute' => 'created_at',
                'label' => 'Ora',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 100px;'],
                'value'=>function($data) {
                    return Yii::$app->formatter->astime($data['created_at']);
                }
            ],[
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
                'attribute' => 'tipologia',
                'label' => 'Tipo di intervento richiesto',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 150px;']
            ],
            [
                'attribute' => 'targa',
                'label' => 'Targa mezzo attivato',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 150px;']
            ],
            [
                'attribute' => 'motivazione_rifiuto',
                'label' => 'Motivo del rifiuto',
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 150px;'],
                'value'=>function($data) {
                    return UtlIngaggio::replaceMotivazioneRifiuto($data['motivazione_rifiuto']);
                }
            ]
        ];

require_once '_data.php';
?>