<?php 


use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\helpers\Url;


use yii\bootstrap\Modal;
use yii\widgets\Pjax;


if(Yii::$app->user->can('manageRecapitiOrgs')) {
        echo Html::button(
                    '<i class="glyphicon glyphicon-plus"></i> Nuovo contatto',
                    [
                        'title' => 'Nuovo contatto',
                        'class' => 'contattoModal btn btn-success',
                        'style' => 'margin-bottom: 18px'
                    ]
                );

        Pjax::begin();

        Modal::begin([
            'id' => 'modal-contatto',
            'header' => '<h2 class="p10w">Inserisci un contatto</h2>',
            'size' => 'modal-md'
        ]);

        echo Yii::$app->controller->renderPartial('_form-rubrica_contatto', [
            'model'=>$model
        ]);
        
        Modal::end();

        Pjax::end();
    }

    