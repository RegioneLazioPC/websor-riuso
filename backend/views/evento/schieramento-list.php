<?php 
use common\models\ConMezzoSchieramento;
use common\models\UtlAutomezzoSearch;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\UtlCategoriaAutomezzoAttrezzatura;
use common\models\UtlAutomezzoTipo;
use common\models\UtlAttrezzaturaTipo;
use common\models\tabelle\TblTipoRisorsaMeta;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$add_js = '';
if(isset($reload_pjax_main)) {

    $add_js = "<script type=\"text/javascript\">
        $(document).ready(function() {
            $.pjax.reload({ container:'#lista-ingaggi-pjax', timeout:60000 });
        });
    </script>";
    
}

$err = '';
if(isset($error_message)) {
    $err = '<p class="text-danger">'.$error_message.'</p>';
}

$cols = [[
        'class' => 'yii\grid\ActionColumn',
        'template' => '{add}',
        'buttons' => [
            'add' => function ($url, $resource) use ($model) {
                $evento = null;
                try {
                    if($resource['tipo'] == 'mezzo'){
                        $evento = $resource->ingaggioMezzo->evento;
                    } else {
                        $evento = $resource->ingaggioAttrezzatura->evento;
                    }
                } catch(\Exception $e) {
                    
                }

                if(!empty($evento) && $evento->id == $model->id) {
                    return '';
                } else {
                    if (Yii::$app->user->can('activateSchieramento')) {
                        $url = ['evento/activate-schieramento', 
                            'id_evento' => $model->id, 
                            'id_risorsa' => $resource->uid
                        ];
                        return Html::a('<span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;', $url, [
                            'title' => Yii::t('app', 'Attiva'),
                            'data-toggle' => 'tooltip',
                            'data' => [
                                'confirm' => 'Sicuro di voler attivare questo elemento?',
                                'method' => 'post',
                                'pjax' => true
                            ]
                        ]);
                    } else {
                        return '';
                    }
                }
            }
        ],
    ]];

$array_filters = [];
if (!empty(Yii::$app->request->get('meta'))) {
    foreach (Yii::$app->request->get('meta') as $meta_key => $meta_filter) {
        if (!empty($meta_filter)) $array_filters[$meta_key] = $meta_filter;
    }
}

$types = ['Mezzi'=>[],'Attrezzature'=>[]];
$tipi_mezzi = UtlAutomezzoTipo::find()->asArray()->orderBy(['descrizione'=>SORT_ASC])->all();
$tipi_attrezzature = UtlAttrezzaturaTipo::find()->asArray()->orderBy(['descrizione'=>SORT_ASC])->all();

foreach ($tipi_mezzi as $tipo) $types['Mezzi']["mezzo_" . $tipo['id']] = $tipo['descrizione'];

foreach ($tipi_attrezzature as $tipo) $types['Attrezzature']["attrezzatura_" . $tipo['id']] = $tipo['descrizione'];

$meta_to_show = TblTipoRisorsaMeta::find()->where(['show_in_column' => 1])->all();
$cols = array_merge($cols, [
    [
        'label' => 'Targa/Modello',
        'attribute' => 'identifier'
    ],
    [
        'label' => 'Tipologia',
        'attribute' => 'idtipo',
        'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
        'filter' => Html::activeDropDownList($searchModel, 'idtipo', $types, ['class' => 'form-control', 'prompt' => 'Tutti']),
        'value' => function ($data) {
            if($data['tipo'] == 'mezzo'){
                return $data->tipoMezzo->descrizione;
            } else {
                return $data->tipoAttrezzatura->descrizione;
            }
            return null;
        }
    ],
    'schieramento',
    [
        'label' => 'Attivazione',
        'attribute' => '_id',
        'format'=>'raw',
        'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
        'value' => function($data) {
            $ingaggio = null;
            try {
                if($data['tipo'] == 'mezzo'){
                    $ingaggio = $data->ingaggioMezzo;
                } else {
                    $ingaggio = $data->ingaggioAttrezzatura;
                }
            } catch(\Exception $e) {
                return null;
            }

            if(!empty($ingaggio)) {
                $url_evento = ['evento/view', 'id' => @$ingaggio->evento->id];
                $url_attivazione = ['ingaggio/view', 'id' => $ingaggio->id];
                $i = !empty($ingaggio->evento->indirizzo) ? $ingaggio->evento->indirizzo : $ingaggio->evento->luogo;
                $comune = " " . $ingaggio->evento->comune->comune . " (".$ingaggio->evento->comune->provincia_sigla.")";
                $str = 'Attivazione su evento ' . $ingaggio->evento->num_protocollo . ' ' . $i . ' ' . $comune;
                return /*Html::a($ingaggio->evento->num_protocollo, $url_evento, [
                    'title' => Yii::t('app', 'Dettaglio evento'),
                    'data-toggle' => 'tooltip',
                    'data-pjax' => 0,
                    'target'=>'_blank'
                ]) . " " . */Html::a("$str", $url_attivazione, [
                    'title' => Yii::t('app', 'Dettaglio attivazione'),
                    'data-toggle' => 'tooltip',
                    'data-pjax' => 0,
                    'target'=>'_blank'
                ]);
            }
            return null;
        }
    ],
    [
            'label' => "Meta dati",
            'attribute' => '_meta',
            'format' => 'raw',
            'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
            'value' => function ($model) use ($meta_to_show) {
                $list = [];
                foreach ($meta_to_show as $meta) {
                    if(isset($model->_meta[$meta->key]) && !empty($model->_meta[$meta->key])){
                        $list[] = "<b>" . $meta->label . "</b> " . $model->_meta[$meta->key];
                    }
                }
                return implode("<br />", $list);
            }
        ],
    /*[
        'label' => 'Tipo risorsa',
        'attribute' => 'tipo',
        'filter' => Html::activeDropDownList($searchModel, 'tipo', ['mezzo'=>'Mezzo','attrezzatura'=>'Attrezzatura'], ['class' => 'form-control', 'prompt' => 'Tutti'])
    ],*/            
    
    
    [
        'label' => 'Organizzazione',
        'attribute' => 'organizzazione',
        'format' => 'raw',
        'width' => '300px',
        'contentOptions' => ['style' => 'width:200px; white-space: normal;']
    ],
    [
        'label' => 'Contatti',
        'attribute' => 'contatti',
        'format' => 'raw',
        'width' => '300px',
        'value' => function ($data) {
            $org = $data->getOrganizzazione()->one();
            $str = (!empty($org)) ? "Ambito: " . $org->ambito : '';
            foreach ($data->getContattiAttivazioni()->all() as $con_contatto) {
                $str .= "<br />" . $con_contatto->contatto->contatto;
            }
            return $str;
        }
    ],
    /*[
        'label' => 'Disp. oraria',
        'attribute' => 'disp_oraria',
        'format' => 'raw',
        'width' => '300px',
        'value' => function ($data) {
            return $data->sede->disponibilita_oraria;
        }
    ],*/
    [
        'label' => 'Specializzazioni',
        'attribute' => 'specializzazioni',
        'format' => 'raw',
        'contentOptions' => ['style' => 'width:500px; white-space: normal;'],
        'value' => function ($data) {
            $org = $data->getOrganizzazione()->one();
            if(empty($org)) return '';

            $list = [];
            foreach ($org->getSezioneSpecialistica()->all() as $sezione) {
                $list[] = $sezione->descrizione;
            }
            return implode(",<br />", $list);
        }
    ]
]);

?>
<div class="risorse-list">

   
	<?= GridView::widget([
    	'id'=>'avaible-risorse-list',
        'dataProvider' => $dataProvider,
        'export' => false,
        'exportConfig' => ['csv' => true, 'xls' => true, 'pdf' => true],
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'enablePushState' => false,
            ]
        ],
        'panel' => [
            'heading' => '<h2 class="panel-title">Lista risorse</h2>',
            'before' => $add_js.$err
        ],
        'columns' => $cols
    ]); ?>

</div>
