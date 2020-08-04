<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AlmAllertaMeteo */

$this->title = Yii::$app->formatter->asDate($model->data_allerta);
$this->params['breadcrumbs'][] = ['label' => 'Allerte meteo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alm-allerta-meteo-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        
        <?= Html::a('Elimina', ['delete', 'id' => $model->id], [
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
            'id',
            //'protocollo',
            //'num_documento',
            'data_allerta:date',
            [
                'label' => 'Note',
                'format' => 'ntext',
                'attribute' => 'messaggio',
            ],
            'data_creazione:date',
            'lat',
            'lon',
            [
                'label' => 'File',
                'attribute' =>'id_media',
                'format' => 'raw',
                'value' => function($model){
                    if(!empty($model->file)) {
                        $str = '';
                        foreach ($model->file as $media) {
                            $str .= "<p>" . Html::encode($media->nome) . 
                                 Html::a('Vedi allegato', ['media/view-media', 'id' => $media->id], ['class' => 'btn btn-primary btn-xs', 'style'=>'margin-left: 10px', 'target'=>'_blank', 'data-pjax'=>0]) .
                            "</p>";
                        }
                        return $str;
                    } else {
                        return " - ";
                    }
                }
            ],
        ],
    ]) ?>

</div>
