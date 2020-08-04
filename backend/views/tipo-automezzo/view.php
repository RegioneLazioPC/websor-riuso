<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;

use common\models\UtlAggregatoreTipologie;
/* @var $this yii\web\View */
/* @var $model common\models\UtlAutomezzoTipo */


$aggregati = $model->getAggregatori()->all();

$aggregati_id = [];
$aggregati_descrizioni = [];

foreach ($aggregati as $aggr) {
    $aggregati_id[] = $aggr->id;
    $aggregati_descrizioni[] = $aggr->descrizione;
}

$this->title = $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Utl Automezzo Tipos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-automezzo-tipo-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('updateTipoAutomezzo')) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if(Yii::$app->user->can('deleteTipoAutomezzo')) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler cancellare questo elemento?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'descrizione',
            [
                'label' => 'Tipologie',
                'attribute' =>'id',
                'value' => function($model,$aggregati_descrizioni) {
                            $aggs = [];
                            $aggregati = $model->getAggregatori()->all();
                            foreach ($aggregati as $aggr) {
                                $aggs[] = $aggr->descrizione . " - " . $aggr->categoria->descrizione;
                            }
                            return implode(", ", $aggs);
                        }
            ],
        ],
    ]) ?>


    
    <div class="clear"></div>

    <div class="row">
        <div class="col-sm-6">
            <?php 
             if(Yii::$app->user->can('updateTipoAutomezzo')) :
                $form = ActiveForm::begin(); ?>

                <?php

                $options = UtlAggregatoreTipologie::find()
                            ->andWhere(['not in', 'id', $aggregati_id])
                            ->all();

                $list = [];
                foreach ($options as $opt) {
                    if(!isset($list[$opt->categoria->descrizione])) $list[$opt->categoria->descrizione] = [];

                    $list[$opt->categoria->descrizione][$opt->id] = $opt->descrizione;
                }

                    echo $form->field($model, 'aggregatore', ['options' => ['class'=>'no-pl']])->widget(Select2::classname(), [
                        'data' => $list,
                        'options' => [
                            'multiple' => true,
                            'placeholder' => 'Seleziona una tipologia...'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                ?>   

                <div class="form-group">
                    <?= Html::submitButton('Aggiungi', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); 
            endif;
            ?>     
        </div>
        <div class="col-sm-6">
            <ul class="list-unstyled">
            <?php 
            foreach ($aggregati as $aggregato) {
                $res = '<li>'.Html::encode($aggregato->descrizione);
                if(Yii::$app->user->can('updateTipoAutomezzo')) $res .= " " . Html::a('<span class="fa fa-close"></span>&nbsp;&nbsp;', ['tipo-automezzo/unlink?id='.$model->id.'&aggregatore='.$aggregato->id], [
                                    'title' => Yii::t('app', 'Elimina'),
                                    'data-toggle'=>'tooltip',
                                ]);

                $res .= '</li>';
                echo $res;
            }
            ?>
        </ul>
        </div>
    </div>

    

</div>
