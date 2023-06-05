<?php

use common\models\SalaOperativaEsterna;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */

$this->title = 'Assegna evento N. Protocollo ' . $model->num_protocollo;
$this->params['breadcrumbs'][] = ['label' => 'Lista eventi', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Assegna evento a sala comunale';
?>
<div class="utl-evento-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        // 'action' => ['evento/create-task-mattinale'],
        // 'validationUrl' => ['evento/validate-create-task-mattinale'],
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'id' => 'assegnaSalaEsterna'
    ]); ?>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

            <div class="row m5w m20h bg-grayLighter box_shadow">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">
                    <?php
                    $sale = SalaOperativaEsterna::find()->all();
                    if(count($sale) > 0) {
                        $data = ArrayHelper::map(SalaOperativaEsterna::find()->all(), 'id', 'nome');
                        echo $form->field($model, 'saleOperativeEsterne')->widget(Select2::class, [
                            'data' => $data,
                            'options' => [
                                'placeholder' => 'Seleziona una sala comunale...',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ])->label('Seleziona sala comunale a cui assegnare l\'evento');
                    } else {
                        ?>
                        <p class="text-danger">Non sono presenti sale operative esterne configurate</p>
                        <?php 
                    }
                    ?>
                </div>
            </div>

        </div>

    </div>

    <div class="form-group">
        <?php 
        if(count($sale) > 0) {
            
            echo Html::a('<i class="fa fa-refresh p5w"></i> Annulla', ['index'], ['class' => 'btn btn-default']); 
            echo " " . Html::submitButton($model->isNewRecord ? '<i class="fa fa-save p5w"></i> Salva' : 'Assegna evento', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']); 

        }
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>