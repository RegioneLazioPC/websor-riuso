<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\helpers\Url;

use yii\helpers\ArrayHelper;

use common\models\UtlCategoriaAutomezzoAttrezzatura;
use common\models\UtlAutomezzoTipo;
use common\models\UtlAttrezzaturaTipo;
/* @var $this yii\web\View */
/* @var $model common\models\VolSede */

$this->title = $model->indirizzo;
$this->params['breadcrumbs'][] = ['label' => 'Sedi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vol-sede-view">

    <h1><?= Html::encode($this->title) ?></h1>
   

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Organizzazione',
                'attribute' =>'id_organizzazione',
                'value' => $model->organizzazione->denominazione
            ],
            'indirizzo',
            [
                'label' => 'Comune',
                'attribute' =>'comune',
                'value' => $model->locComune->comune . ' ('.$model->locComune->provincia->sigla.')'
            ],
            'cap',
            'tipo',
            'email:email',
            'email_pec:email',
            [
                'label' => 'Specializzazione',
                'attribute' =>'id_specializzazione',
                'value' => ($model->specializzazione) ? $model->specializzazione->descrizione : ''
            ],
            'telefono',
            'altro_telefono',
            'cellulare',
            'fax',
            'altro_fax',
            'sitoweb',
            'disponibilita_oraria',
            'lat',
            'lon',
            'coord_x',
            'coord_y',
        ],
    ]) ?>


    <?= GridView::widget([
        'dataProvider' => $automezzodataProvider,
        'filterModel' => $automezzosearchModel,
        'responsive'=>true,
        'hover'=>true,
        'panel' => [
            'heading'=>'<h2 class="panel-title">Automezzi</h2>',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'targa',
            'data_immatricolazione',
            [
                'label' => 'Modello',
                'attribute' => 'modello',
                'format' => 'raw',
                'value' => function($data){
                    return '<span style="max-width: 200px; display: block; white-space: pre-wrap;">'.Html::encode($data['modello']).'</span>';
                }
            ],
            'classe',
            'sottoclasse',
            [
                'label' => 'Tipo',
                'attribute' => 'idtipo',
                'filter'=> Html::activeDropDownList($automezzosearchModel, 'idtipo', ArrayHelper::map(UtlAutomezzoTipo::find()->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data) {
                    return $data['tipo']['descrizione'];
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}',//{update}{delete}
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'view') {
                        $url = Url::to(['automezzo/view', 'id'=>$model->id]); 
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $attrezzaturadataProvider,
        'filterModel' => $attrezzaturasearchModel,
        'responsive'=>true,
        'hover'=>true,
        'panel' => [
            'heading'=>'<h2 class="panel-title">Attrezzature</h2>',
            
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'label' => 'Tipo',
                'attribute' => 'idtipo',
                'filter'=> Html::activeDropDownList($attrezzaturasearchModel, 'idtipo', ArrayHelper::map(UtlAttrezzaturaTipo::find()->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data) {
                    return $data['tipo']['descrizione'];
                }
            ],
            [
                'label' => 'Modello',
                'attribute' => 'modello',
                'format' => 'raw',
                'value' => function($data){
                    return '<span style="max-width: 200px; display: block; white-space: pre-wrap;">'.Html::encode($data['modello']).'</span>';
                }
            ],
            'classe',
            'sottoclasse',
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}',//{update}{delete}
                'urlCreator' => function ($action, $model, $key, $index) {
                    
                    if ($action === 'view') {
                        $url = Url::to(['attrezzatura/view', 'id' => $model->id]); 
                        return $url;
                    }
                    
                }
            ],
        ],
    ]); ?>

</div>
