<?php

use common\models\ConOperatoreEvento;
use common\models\LocProvincia;
use common\models\UtlEvento;
use common\models\UtlTipologia;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use kartik\export\ExportMenu;

use common\models\LocComune;
use common\models\UtlIngaggio;

use common\models\UtlAutomezzoTipo;
use common\models\UtlAttrezzaturaTipo;
use common\models\UtlAggregatoreTipologie;

use yii\data\ArrayDataProvider;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlEventoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Elicotteri';
$this->params['breadcrumbs'][] = $this->title;
/*
$js = "$(document).ready(function() {
            setInterval( function refresh() {
               $.pjax.reload({ container:'#lista-elicotteri-pjax', timeout:60000 })
            },30000);
      });
";
*/
$js = "";
$this->registerJs($js, $this::POS_READY);

$cols = [
            [
                'attribute' => 'elicottero',
                'label' => 'Elicottero',
                'format' => 'raw',
                'value'=>function($record) {
                    return !empty($record['elicottero']) ? $record['elicottero'] : '';
                }
            ],  
            [
                'attribute' => 'destinazione',
                'label' => 'Destinazione',
                'format' => 'raw',
                'value'=>function($record) {
                    return !empty($record['destinazione']) ? $record['destinazione'] : '';
                }
            ],   
            [
                'attribute' => 'ora_decollo',
                'label' => 'Ora decollo',
                'format' => 'raw',
                'value'=>function($record) {
                    return !empty($record['ora_decollo']) ? $record['ora_decollo'] : '';
                }
            ],   
            [
                'attribute' => 'durata_missione',
                'label' => 'Tempo di volo effettivo',
                'format' => 'raw',
                'value'=>function($record) {
                    return !empty($record['durata_missione'] && $record['durata_missione'] != '') ? $record['durata_missione'] : '';
                }
            ],   
            [
                'attribute' => 'durata_totale',
                'label' => 'Durata totale',
                'format' => 'raw',
                'value'=>function($record) {
                    return !empty($record['durata_totale'] && $record['durata_totale'] != ' min') ? $record['durata_totale'] : '';
                }
            ],   
            [
                'attribute' => 'scheda_coau',
                'label' => 'Scheda coau',
                'format' => 'raw',
                'value' => function($record) {
                	return $record['scheda_coau'] ? 'SI' : 'NO';
                }
            ],   
            [
                'attribute' => 'protocollo_evento',
                'label' => 'N. protocollo evento',
                'format' => 'raw',
                'value'=>function($record) {
                    return !empty($record['protocollo_evento']) ? $record['protocollo_evento'] : '';
                }
            ], 
            [
                'attribute' => 'ore_di_volo',
                'label' => 'Totale ore di volo',
                'format' => 'raw',
                'value'=>function($record) {
                    return !empty($record['ore_di_volo']) ? $record['ore_di_volo'] : '';
                }
            ] 
        ];


?>
<div class="lista-elicotteri-videowall">
<?php echo GridView::widget([
        'id' => 'lista-elicotteri',
        'dataProvider' => $dataProvider,
        'responsive'=>false,
        'hover'=>true,
        'perfectScrollbar' => false,
        'perfectScrollbarOptions' => [],
        'toggleData'=>false,
        'floatHeader' => true,
        'containerOptions' => [
            'style'=>'height: 80vh; overflow: auto;',
            'class'=>'superbig-table'
        ],
        'summary'=>'',
        'floatOverflowContainer' => true,
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv'=>true, 'xls'=>true, 'pdf'=>true],
        'panel' => [
            'heading'=> "ELICOTTERI ATTUALMENTE IN VOLO",
            'footer'=>false,
        ],
        'pjax'=>true,
        'pjaxSettings'=>[
            'neverTimeout'=> true,
        ],
        'export'=> false,
        'columns' => $cols
    ]); ?>
</div>