<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\UtlIngaggio */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Ingaggi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$js = '


    $("#change_chiusura_date").click( function() {
        $("#chiusura_date_form").show();
    })

';
$this->registerJs($js, $this::POS_READY);
?>
<div class="utl-ingaggio-view">

    
    <p>
        <?php echo Html::a('Dettagli evento', ['evento/view', 'id'=>$model->idevento], ['class' => 'btn btn-default']);?>
        <?php echo Html::a('Gestione evento', ['evento/gestione-evento', 'idEvento'=>$model->idevento], ['class' => 'btn btn-success']);?>
    </p>


    <p>
        <?php if ($model->stato != 3 && $model->stato != 2) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?php if ($model->stato != 3) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler eliminare questo elemento?',
                'method' => 'post',
            ],
        ]); ?>
    </p>


    

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'idevento',
            [
                'label'  => 'Organizzazione',
                'value'  => ($model->organizzazione) ? $model->organizzazione->denominazione : ""
            ],
            [
                'label'  => 'Sede',
                'value'  => ($model->sede) ? $model->sede->indirizzo . " - " . $model->sede->tipo : ""
            ],
            [
                'label'  => 'Automezzo',
                'value'  => ($model->automezzo) ? $model->automezzo->tipo->descrizione . " - " . $model->automezzo->targa : ""
            ],
            [
                'label'  => 'Attrezzatura',
                'value'  => ($model->attrezzatura) ? $model->attrezzatura->tipo->descrizione : ""
            ],
            'note:ntext',
            [
                'label' => 'Stato',
                'attribute' => 'stato',
                'value' => function($model) {
                    if($model->stato != 2) return $model->getStato();

                    $ret = $model->getStato();
                    $ret .= " - " . $model->getMotivazioneRifiuto();
                    if($model->motivazione_rifiuto == 5) $ret .= " - " . $model->motivazione_rifiuto_note;

                    return $ret;
                }
            ],
            [
                'label'  => 'Data',
                'value'  => $model->created_at
            ],
            [
                'label'  => 'Chiusura',
                'value'  => $model->closed_at
            ],
        ],
    ]) ?>

    <?php
    
    if(Yii::$app->user->can('adminPermissions') || Yii::$app->user->can('changeDateAttivazioni')){
        echo Html::button('Modifica data di chiusura e apertura', ['class' => 'btn btn-default', 'id'=>'change_chiusura_date']);

        ?>
        <div id="chiusura_date_form" style="display: none;">
        <?php
            echo $this->render('_form_change_data_chiusura', [
                'model' => $model,
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel
            ]); 
        ?>
        </div>
    <?php
    }
    ?>


    <?= $this->render('_ingaggio_volontari', [
        'model' => $model,
        'dataProvider' => $dataProvider,
        'searchModel' => $searchModel
    ]) ?>

</div>
