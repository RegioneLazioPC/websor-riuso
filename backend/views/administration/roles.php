<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;


use common\models\UtlCategoriaAutomezzoAttrezzatura;
use common\models\UtlAutomezzoTipo;

use common\models\tabelle\TblTipoRisorsaMeta;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UtlAutomezzoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ruoli';
$this->params['breadcrumbs'][] = $this->title;




$cols = [
    ['class' => 'yii\grid\SerialColumn'],
    'name',
    'description'
];



$cols = array_merge($cols, [
    [
        'label' => 'Ruolo amministrativo',
        'attribute' => 'administrative',
        'filter' => false,
        'value' => function ($data) {
            return ($data->administrative == 1) ? 'Si' : 'No';
        }
    ],
    [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{administrative}',
        'buttons' => [
            'administrative' => function ($url, $model) {
                return Html::a(
                    '<span class="fa fa-arrow-'.($model->administrative == 1  ? 'down' : 'up').'"></span>&nbsp;&nbsp;',
                    ['administration/set-administrative?name=' . $model->name],
                    [
                            'title' => Yii::t('app', $model->administrative == 0 ? 'Rendi amministrativo' : 'Rendi non amministrativo'),
                            'data-toggle' => 'tooltip'
                    ]
                );
            }
        ]
    ]
]);






?>
<div class="utl-automezzo-index">

    <h1><?= Html::encode($this->title); ?></h1>

    <p>
        <?php if (Yii::$app->user->can('Admin')) {
            echo Html::a('Scarica mappatura', ['download-map'], ['class' => 'btn btn-warning', 'target'=>'_blank']);
        } ?>
    </p>
    <p>
        <b>Scarica PDF accessi: </b> 
        <?php if (Yii::$app->user->can('Admin')) {
            echo Html::a('Ultimi 6 mesi', ['download-format?format=pdf&time=6'], ['class' => 'btn btn-info', 'target'=>'_blank']);
        } ?>
        <?php if (Yii::$app->user->can('Admin')) {
            echo Html::a('Ultimi 12 mesi', ['download-format?format=pdf&time=12'], ['class' => 'btn btn-info', 'target'=>'_blank']);
        } ?>
    </p>
    <p>
        <b>Scarica Excel accessi: </b> 
        <?php if (Yii::$app->user->can('Admin')) {
            echo Html::a('Ultimi 6 mesi', ['download-format?format=xls&time=6'], ['class' => 'btn btn-success', 'target'=>'_blank']);
        } ?>
        <?php if (Yii::$app->user->can('Admin')) {
            echo Html::a('Ultimi 12 mesi', ['download-format?format=xls&time=12'], ['class' => 'btn btn-success', 'target'=>'_blank']);
        } ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'export' => Yii::$app->user->can('exportData') ? [] : false,
        'exportConfig' => ['csv' => true, 'xls' => true, 'pdf' => true],
        'filterModel' => $searchModel,
        'perfectScrollbar' => true,
        'perfectScrollbarOptions' => [],
        'columns' => $cols
    ]); ?>
</div>