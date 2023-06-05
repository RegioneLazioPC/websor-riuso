<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\bootstrap\Tabs;



$js = "

$.pjax.defaults.timeout = 60000

$(document).on('click', '.modalRisorse', function(e) { 
    e.preventDefault();
    $('#modal-add-resource').modal('show')
        .find('#modalContent')
        .load($(this).attr('value'));
    document.getElementById('modalAddHeader').innerHTML = '<h2>' + $(this).attr('title') + '</h2>';
});
";

$this->registerJs($js, $this::POS_READY);



$this->title = $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Lista Schieramenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-sala-operativa-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php 
        if(Yii::$app->user->can('updateSchieramento')) echo Html::a('Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?php 
        if(Yii::$app->user->can('deleteSchieramento')) echo Html::a('Cancella', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler eliminare questo elemento?',
                'method' => 'post',
            ],
        ]); ?>
        <?php echo Html::a('Annulla', ['index'], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'descrizione',
            //[
            //    'attribute'=>'data_validita',
            //    'format'=>'date'
            //],
            [
                'label'=>'Data creazione',
                'attribute'=>'created_at',
                'format'=>'datetime'
            ],
            [
                'label'=>'Data ultimo aggiornamento',
                'attribute'=>'updated_at',
                'format'=>'datetime'
            ]
        ],
    ]) 
    ?>

    <?php 
        echo Yii::$app->user->can('updateSchieramento') ? Html::button(
                            '<i class="glyphicon glyphicon-plus"></i> Aggiungi mezzo',
                            [
                                'title' => Yii::t('app', 'Aggiungi mezzo'),
                                'class' => 'modalRisorse btn btn-success',
                                'style' => 'margin-right: 20px;',
                                'value' => Url::toRoute(['schieramento/add-mezzo-list', 'id' => $model->id])
                            ]
                        ) . Html::button(
                            '<i class="glyphicon glyphicon-plus"></i> Aggiungi attrezzatura',
                            [
                                'title' => Yii::t('app', 'Aggiungi attrezzatura'),
                                'class' => 'modalRisorse btn btn-warning',
                                'value' => Url::toRoute(['schieramento/add-attrezzatura-list', 'id' => $model->id])
                            ]
                        ) : '';



        ?>
        <div style="margin-top: 24px;">
        <?php 
        echo Tabs::widget([
            'items' => [
                [
                    'label' => 'Mezzi',
                    'content' => $this->render('_partial_mezzi-schieramento', [
                        'model' => $model,
                        'search_mezzo'=>$search_mezzo,
                        'mezzo_data_provider'=>$mezzo_data_provider
                    ]),
                    'active' => (!isset($_GET['tab']) || $_GET['tab'] == 'mezzi') ? true : false
                ],
                [
                    'label' => 'Attrezzature',
                    'content' => $this->render('_partial_attrezzature-schieramento', [
                        'model' => $model,
                        'search_attrezzatura'=>$search_attrezzatura,
                        'attrezzatura_data_provider'=>$attrezzatura_data_provider
                    ]),
                    'active' => (isset($_GET['tab']) && $_GET['tab'] == 'attrezzature') ? true : false
                ]

            ],
        ]);
         ?>
        </div>
        <?php


        Modal::begin([
            'id' => 'modal-add-resource',
            'headerOptions' => ['id' => 'modalAddHeader'],
            'size' => 'modal-lg',
            'options' => [
                'class' => 'ultra-large-modal'
            ],
            
        ]);

            echo "<div id='modalContent'></div>";
            
        Modal::end();

    ?>
</div>