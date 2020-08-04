<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\data\ActiveDataProvider;

/* @var $this yii\web\View */
/* @var $model common\models\UtlOperatorePc */

$this->title = 'Dettaglio utente '.$model->nome.' '.$model->cognome.' Matr.'.$model->matricola;
$this->params['breadcrumbs'][] = ['label' => 'Utl Operatore Pcs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-operatore-pc-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Cancella', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler eliminare questo elemento?',
                'method' => 'post',
            ],
        ]) ?>
        <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label' => 'Matricola',
                'attribute' => 'anagrafica.matricola'
            ],
            [
                'label' => 'Nome',
                'attribute' => 'anagrafica.nome'
            ],
            [
                'label' => 'Cognome',
                'attribute' => 'anagrafica.cognome'
            ],
            [
                'label' => 'Sala operativa',
                'attribute' => 'salaoperativa.nome'
            ],
            'ruolo',
        ],
    ]) ?>

    <div class="clearfix"></div>
        
    <div class="row">
        <div class="col-sm-12"><h3>Contatti</h3></div>
    <?php 
        
        echo $this->render('_list-contatto', [
            'dataProvider' => new ActiveDataProvider([
                'query' => \common\models\operatore\ConOperatorePcContatto::find()->where(['id_operatore_pc'=>$model->id])
            ]), 
            'model'=>$model
        ]);       
    ?>
    </div>

    <?php 

    echo $this->render('/everbridge/index', ['model'=>$model]);

    ?>

</div>
