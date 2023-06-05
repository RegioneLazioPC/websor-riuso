<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;


use yii\helpers\ArrayHelper;
use common\models\UtlCategoriaAutomezzoAttrezzatura;
use common\models\UtlAttrezzaturaTipo;
/* @var $this yii\web\View */
/* @var $model common\models\UtlAutomezzo */

$this->title = $model->targa;
$this->params['breadcrumbs'][] = ['label' => 'Automezzi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-automezzo-view">

    <h1><?= Html::encode($this->title) ?></h1>

    
    <p>
        <?php if(Yii::$app->user->can('updateAutomezzo') && empty($model->id_sync)) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if(Yii::$app->user->can('deleteAutomezzo') && empty($model->id_sync)) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler eliminare questo elemento?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'targa',
            'data_immatricolazione',
            //'idsquadra',
            'device_id',
            'classe',
            'sottoclasse',
            'modello',
            [
                'label' => 'Tipo',
                'attribute' =>'tipo',
                'value' => $model->tipo->descrizione
            ],
            'capacita',
            [
                'label' => 'In uso',
                'attribute' =>'engaged',
                'value' => ($model->engaged) ? "Si" : "No"
            ],
            [
                'label' => 'Organizzazione',
                'attribute' =>'idorganizzazione',
                'value' => ($model->organizzazione) ? $model->organizzazione->denominazione : '-'
            ],
            [
                'label' => 'Sede',
                'attribute' =>'idsede',
                'value' => ($model->sede) ? $model->sede->tipo . " - " . $model->sede->indirizzo : '-'
            ],
        ],
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive'=>true,
        'hover'=>true,
        'panel' => [
            'heading'=>'<h2 class="panel-title">Attrezzature</h2>',
            
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'label' => 'Categoria',
                'attribute' => 'idcategoria',
                'filter'=> Html::activeDropDownList($searchModel, 'idcategoria', ArrayHelper::map(UtlCategoriaAutomezzoAttrezzatura::find()->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data) {
                    return $data['categoria']['descrizione'];
                }
            ],
            [
                'label' => 'Tipo',
                'attribute' => 'idtipo',
                'filter'=> Html::activeDropDownList($searchModel, 'idtipo', ArrayHelper::map(UtlAttrezzaturaTipo::find()->asArray()->all(), 'id', 'descrizione'), ['class' => 'form-control','prompt' => 'Tutti']),
                'value' => function($data) {
                    return $data['tipo']['descrizione'];
                }
            ],
            [
                'label' => 'Modello',
                'attribute' => 'modello',
                'format' => 'raw',
                'value' => function($data){
                    return '<span style="max-width: 200px; display: block; white-space: pre-wrap;">'.$data['modello'].'</span>';
                }
            ],
            'classe',
            'sottoclasse',
            [
                'label' => 'Org.',
                'attribute' => 'org',
                'format' => 'raw',
                'value' => function($data){
                    return '<span style="max-width: 200px; display: block; white-space: pre-wrap;">'.$data['organizzazione']['denominazione'].'</span>';
                }
            ],
            ['class' => 'yii\grid\ActionColumn', 'template'=>'{view}'],
        ],
    ]); ?>

</div>
