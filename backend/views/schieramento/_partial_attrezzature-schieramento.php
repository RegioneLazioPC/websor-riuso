<?php 
use common\models\ConMezzoSchieramento;
use common\models\UtlAutomezzoSearch;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use common\models\UtlCategoriaAutomezzoAttrezzatura;
use common\models\UtlAttrezzaturaTipo;
use common\models\tabelle\TblTipoRisorsaMeta;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;


$cols = [[
        'class' => 'yii\grid\ActionColumn',
        'template' => '{delete}',
        'buttons' => [
            'delete' => function ($url, $resource) use ($model) {
                if (Yii::$app->user->can('updateSchieramento')) {
                    $url = ['schieramento/remove-attrezzatura', 'id' => $model->id, 'id_attrezzatura' => $resource->attrezzatura->id];
                    return Html::a('<span class="fa fa-trash"></span>&nbsp;&nbsp;', $url, [
                        'title' => Yii::t('app', 'Rimuovi'),
                        'data-toggle' => 'tooltip',
                        'data' => [
                            'confirm' => 'Sicuro di voler eliminare questo elemento?'
                        ],
                    ]);
                } else {
                    return '';
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


$meta_to_show = TblTipoRisorsaMeta::find()->where(['show_in_column' => 1])->all();
$cols = array_merge($cols, [
    [
        'label' => 'Modello',
        'attribute' => 'modello',
        'format' => 'raw',
        'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
        'value' => function($data){
            return '<span style="max-width: 200px; display: block; white-space: pre-wrap;">'.Html::encode( $data['attrezzatura']['modello'] ) .'</span>';
        }
    ],
    [
        'label' => 'Tipo',
        'attribute' => 'idtipo',
        'filter' => Html::activeDropDownList($search_attrezzatura, 'idtipo', ArrayHelper::map(UtlAttrezzaturaTipo::find()->orderBy(['descrizione'=>SORT_ASC])->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control', 'prompt' => 'Tutti']),
        'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
        'value' => function ($data) {
            return $data['attrezzatura']['tipo']['descrizione'];
        }
    ],
    [
        'label' => 'Valido dal',
        'attribute' => 'date_from',
        'filterType' => GridView::FILTER_DATE,
        'filterWidgetOptions' => [
            'type' => 1,
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'autoclose' => true,
                'todayHighlight' => true,
            ]
        ],
        'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
        'format'=>'date'
    ],
    [
        'label' => 'Valido al',
        'attribute' => 'date_to',
        'filterType' => GridView::FILTER_DATE,
        'filterWidgetOptions' => [
            'type' => 1,
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'autoclose' => true,
                'todayHighlight' => true,
            ]
        ],
        'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
        'format'=>'date'
    ],
    [
        'label' => 'Org.',
        'attribute' => 'org',
        'format' => 'raw',
        'contentOptions' => ['style' => 'width:200px; white-space: normal;'],
        'value' => function($data){
            return '<span style="max-width: 200px; display: block; white-space: pre-wrap;">'.(isset($data['attrezzatura']['organizzazione']) && isset($data['attrezzatura']['organizzazione']['denominazione']) ? Html::encode( $data['attrezzatura']['organizzazione']['denominazione']) : '' ).'</span>';
        }
    ],
    [
        'label' => 'Specializzazioni',
        'attribute' => 'specializzazioni',
        'format' => 'raw',
        'contentOptions' => ['style' => 'width:500px; white-space: normal;'],
        'value' => function ($data) {
            $org = $data->attrezzatura->getOrganizzazione()->one();
            if(empty($org)) return '';

            $list = [];
            foreach ($org->getSezioneSpecialistica()->all() as $sezione) {
                $list[] = $sezione->descrizione;
            }
            return implode(",<br />", $list);
        }
    ],
    [
        'label' => "Meta dati",
        'attribute' => '_meta',
        'format' => 'raw',
        'contentOptions' => ['style' => 'width:400px; white-space: normal;'],
        'value' => function ($model) use ($meta_to_show) {
            $list = [];
            foreach ($meta_to_show as $meta) {
                if(isset($model->attrezzatura->meta[$meta->key]) && !empty($model->attrezzatura->meta[$meta->key])){
                    $list[] = "<b>" . $meta->label . "</b> " . $model->attrezzatura->meta[$meta->key];
                }
            }
            return implode("<br />", $list);
        }
    ]
]);


?>
<div class="attrezzature-list" style="margin-top: 24px;">

    <?php 
    if(isset($error_message)){
        ?>
        <p class="text-danger"><?php echo $error_message;?></p>
        <?php
    }?>
	<?= GridView::widget([
    	'id'=>'selected-attrezzature-list',
        'dataProvider' => $attrezzatura_data_provider,
        'filterModel'=>$search_attrezzatura,
        'export' => false,
        'exportConfig' => ['csv' => true, 'xls' => true, 'pdf' => true],
        'perfectScrollbar' => false,
        'perfectScrollbarOptions' => [],
        'panel' => [
            'heading' => '<h2 class="panel-title">Lista attrezzature</h2>',
            'before' => ''
        ],
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'enablePushState' => false,
            ]
        ],
        'columns' => $cols
    ]); ?>

</div>
