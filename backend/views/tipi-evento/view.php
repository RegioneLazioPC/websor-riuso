<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use kartik\widgets\FileInput;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\UtlTipologia */

$this->title = $model->tipologia;
$this->params['breadcrumbs'][] = ['label' => 'Tipi evento', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="utl-tipologia-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <p>
        <?php if(Yii::$app->user->can('updateTipoEvento')) echo Html::a('Aggiorna', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if(Yii::$app->user->can('deleteTipoEvento')) echo Html::a('Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sicuro di voler rimuovere questo elemento?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tipologia',
            [
                'label' => 'Mostra in app',
                'attribute' =>'idparent',
                'value' => ($model->check_app && $model->check_app == 1) ?  "Si" : "No"
            ],
            'icon_name',
            [
                'label' => 'Genitore',
                'attribute' =>'idparent',
                'value' => ($model->tipologiaGenitore) ?  $model->tipologiaGenitore->tipologia : ""
            ],
        ],
    ]) ?>

    
    <?php 

    if($model->icon_name) :

        ?>
        <div>
            <img src="<?php echo Url::base(true).'/images/markers/'.$model->icon_name;?>" alt="" />
        </div>
        <?php

    endif;

    if (!$model->idparent) :
    ?>
    <div class="col-md-6 col-sm-12">
        <div>
            <?php
            if(!empty($errors)) :                   
                        foreach ($errors as $error) {
                            foreach ($error as $error_message) {
                                echo '<p class="text-danger">'.$error_message.'</p>';
                             } 
                        }
                    endif;

            ?>
        </div>
        <?php
            if(Yii::$app->user->can('addIconTipoEvento') && Yii::$app->user->can('updateTipoEvento')) :
                $form = ActiveForm::begin([
                    'options'=>['enctype'=>'multipart/form-data'] // important
                ]); ?>

                <?php echo $form->field($model, 'icon',['options' => ['class'=>'col-lg-12 no-pl no-pr']])->widget(FileInput::classname(), [
                ])->label('Inserisci immagine'); ?>

                <div class="form-group p5w">
                    <?= Html::submitButton('Inserisci', ['class' => 'btn btn-success']) ?>
                </div>

                <?php 
                ActiveForm::end();
            endif; 
            ?>
        </div>
    <?php
        endif;
        ?>

</div>
