<?php 
use yii\helpers\Html;
use kartik\dialog\Dialog;

echo Dialog::widget(['overrideYiiConfirm' => true]);
$can_manage = Yii::$app->user->can('updateOperatore');
// non cancello se non è di rubrica o è un utl_utente
$btn = ( $can_manage ) ? Html::a(
    'Elimina', 
    ['/operatorepc/delete-contatto-rubrica', 
    	'id'=>$model->id
    ], 
    [
        'data-confirm' => 'Sicuro di voler eliminare questo elemento?',
        'data-method' => 'post',
        'class' => 'btn btn-danger btn-sm'
    ]
) : "";


$set_predefinito = ($can_manage) ? Html::a(
        ($model->contatto->check_predefinito == 1) ? 'Rendi non predefinito' : 'Rendi predefinito', 
        ['/operatorepc/set-default', 
            'id'=>$model->id
        ], 
        [
            'data-confirm' => 'Sicuro di voler confermare questa operazione?',
            'data-method' => 'post',
            'class' => 'btn btn-success btn-sm',
            'style' => 'margin-right: 10px'
        ]
    ) : "";

$set_mobile = (($model->contatto->type == 2 || $model->contatto->type == 4) && ($can_manage)) ?
    Html::a(
        ($model->contatto->check_mobile == 1) ? 'Imposta non cellulare' : 'Imposta cellulare', 
        ['/operatorepc/set-mobile', 
            'id'=>$model->id
        ], 
        [
            'data-confirm' => 'Sicuro di voler confermare questa operazione?',
            'data-method' => 'post',
            'class' => 'btn btn-info btn-sm',
            'style' => 'margin-right: 10px'
        ]
    ) : '';

$set_type = ($can_manage) ?
    Html::a(
        ($model->use_type == 2) ? 'Imposta per messaggistica' : 'Imposta per allertamento', 
        ['/operatorepc/set-use-type', 
            'id'=>$model->id            
        ], 
        [
            'data-confirm' => 'Sicuro di voler confermare questa operazione?',
            'data-method' => 'post',
            'class' => 'btn btn-warning btn-sm',
            'style' => 'margin-right: 10px'
        ]
    ) : '';


echo "<div style=\"\">
<b style=\"line-height: 35px;\">" . Html::encode($model->contatto->tipo()) . ":</b> 
<span style=\"\">" . Html::encode($model->contatto->contatto) . "</span><br />" .$btn." ".$set_predefinito." ".$set_mobile." ".$set_type."</div>";

?>