<?php

use yii\bootstrap\ActiveForm;
use common\models\UtlAutomezzo;
use common\models\UtlFunzioniSupporto;
use common\models\UtlSquadraOperativa;
use common\models\UtlTask;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UtlEvento */
/* @var $form yii\widgets\ActiveForm */


?>

<?php $form = ActiveForm::begin([
    'action' =>['evento/create-task-mattinale'],
    'validationUrl' => ['evento/validate-create-task-mattinale'],
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'id' => 'createTaskMattinale'
]); ?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

        <div class="row m5w m20h bg-grayLighter box_shadow">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 p10h">

                <h5 class="m10h text-uppercase color-gray">Tipo attività</h5>

                <?= $form->field($model, 'idevento')->hiddenInput(['value' => $evento->id])->label(false); ?>
                <?= $form->field($model, 'idoperatore')->hiddenInput(['value' => @Yii::$app->user->identity->operatore->id])->label(false); ?>


                <?php
                echo $form->field($model, 'idfunzione_supporto')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map( UtlFunzioniSupporto::find()->all(), 'id', 'descrizione'),
                    'options' => [
                        'placeholder' => 'Seleziona una funzione di supporto...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label('Funzioni di supporto metodo Augustus:');
                ?>

                <?php
                echo $form->field($model, 'idtask')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map( UtlTask::find()->all(), 'id', 'descrizione'),
                    'options' => [
                        'placeholder' => 'Seleziona ente...',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label('Enti da contattare:');
                ?>

                <?= $form->field($model, 'note')->textarea(); ?>

            </div>
        </div>

    </div>

</div>

<div class="form-group">
    <?php echo Html::a('<i class="fa fa-refresh p5w"></i> Annulla', ['index'], ['class'=>'btn btn-default']); ?>
    <?= Html::submitButton($model->isNewRecord ? '<i class="fa fa-save p5w"></i> Salva' : 'Aggiorna', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>