<?php 
use common\models\UtlTipologia;

require_once '_header.php';
?>



<?php 

// Colonne
$array_tipologie = [];
$array_figlie = [];

$tipologie_genitori = UtlTipologia::find()->where('idparent is null')->all();
foreach ($tipologie_genitori as $tipologia) {
    $array_tipologie[$tipologia->id] = [
        'id' => $tipologia->id,
        'tipologia' => $tipologia->tipologia,
        'children' => []
    ];
}

$tipologie_figlie = UtlTipologia::find()->where('idparent is not null')->all();

foreach ($tipologie_figlie as $tipologia) {
    $array_tipologie[$tipologia->idparent]['children'][$tipologia->id] = [
        'id' => $tipologia->id,
        'tipologia' => $tipologia->tipologia
    ];
    $array_figlie[$tipologia->id] = $tipologia->idparent;
}

$cols = [
    [
        'attribute' => 'provincia',
        'label' => 'Provincia',
        'format' => 'raw',
        'contentOptions' => ['style'=>'width: 80px;']
    ],
    [
        'attribute' => 'comune',
        'label' => 'Comune',
        'format' => 'raw',
        'contentOptions' => ['style'=>'width: 200px;']
    ],
    [
        'attribute' => 'totale',
        'label' => "Totale",
        'format' => 'raw',
        'contentOptions' => ['style'=>'width: 80px;']
    ]
];

$filter_types = false;
$filter_subtypes = false;

$id_tipologia = null;
$id_sottotipologia = null;

$params = Yii::$app->request->get('FilterModel');

if(!empty($params['tipologia'])) {
    $id_tipologia = $params['tipologia'];
    $filter_types = true;
}

if(!empty($params['sottotipologia'])) {
    if(!empty( $params['tipologia'] ) ) $id_tipologia = $array_figlie[ $params['sottotipologia'] ];
    $id_sottotipologia = $params['sottotipologia'];
    $filter_types = true;
    $filter_subtypes = true;
}

foreach ($array_tipologie as $tipologia) {

    if($filter_types && $tipologia['id'] != $id_tipologia) continue;

    foreach ($tipologia['children'] as $figlia) {

        if($filter_subtypes && $figlia['id'] != $id_sottotipologia) continue;

        $cols[] = [
                'attribute' => 'totale_' . $figlia['id'],
                'label' => $figlia['tipologia'] . " (" . $tipologia['tipologia'] . ")",
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;'],
                'value' => function($data) use ($figlia) {
                    return ( isset($data[ 'totale_' . $figlia['id'] ]) ) ? $data[ 'totale_' . $figlia['id'] ] : 0;
                }
            ];
    }

    $cols[] = [
                'attribute' => 'totale_' . $tipologia['id'],
                'label' => "Totale " . $tipologia['tipologia'],
                'format' => 'raw',
                'contentOptions' => ['style'=>'width: 80px;'],
                'value' => function($data) use ($tipologia) {
                    return ( isset($data[ 'totale_' . $tipologia['id'] ]) ) ? $data[ 'totale_' . $tipologia['id'] ] : 0;
                }
            ];
    
}

require_once '_data.php';
?>