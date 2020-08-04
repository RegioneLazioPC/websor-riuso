<?php 
use yii\helpers\Html;
use kartik\dialog\Dialog;

echo Dialog::widget(['overrideYiiConfirm' => true]);
// non cancello se non è di rubrica o è un utl_utente
$btn = ( $elemento->tipo_riferimento == 'id_mas_rubrica' && $model->contatto_type != 'utl_utente' ) ? Html::a(
    'Elimina', 
    ['/mas/delete-contatto-rubrica', 
    	'id_riferimento'=>$model->id_riferimento, 
    	'tipo_riferimento'=>$model->tipo_riferimento, 
    	'id_contatto'=>$model->id_contatto,
    	'contatto_type'=>$model->contatto_type 
    ], 
    [
        'data-confirm' => 'Sicuro di voler eliminare questo elemento?',
        'data-method' => 'post',
        'class' => 'btn btn-danger btn-sm'
    ]
) : "";


$set_predefinito = ($elemento->tipo_riferimento == 'id_mas_rubrica' && $model->contatto_type != 'utl_utente') ? Html::a(
        ($model->check_predefinito == 1) ? 'Rendi non predefinito' : 'Rendi predefinito', 
        ['/mas/set-default', 
            'id_riferimento'=>$model->id_riferimento, 
            'tipo_riferimento'=>$model->tipo_riferimento, 
            'id_contatto'=>$model->id_contatto,
            'contatto_type'=>$model->contatto_type,
            'value' => ($model->check_predefinito == 1) ? 0 : 1
        ], 
        [
            'data-confirm' => 'Sicuro di voler confermare questa operazione?',
            'data-method' => 'post',
            'class' => 'btn btn-success btn-sm',
            'style' => 'margin-right: 10px'
        ]
    ) : "";

$set_mobile = (($model->tipo_contatto == 2 || $model->tipo_contatto == 4) && ($elemento->tipo_riferimento == 'id_mas_rubrica' && $model->contatto_type != 'utl_utente')) ?
    Html::a(
        ($model->check_mobile == 1) ? 'Imposta non cellulare' : 'Imposta cellulare', 
        ['/mas/set-mobile', 
            'id_riferimento'=>$model->id_riferimento, 
            'tipo_riferimento'=>$model->tipo_riferimento, 
            'id_contatto'=>$model->id_contatto,
            'contatto_type'=>$model->contatto_type,
            'value' => ($model->check_mobile == 1) ? 0 : 1
        ], 
        [
            'data-confirm' => 'Sicuro di voler confermare questa operazione?',
            'data-method' => 'post',
            'class' => 'btn btn-info btn-sm',
            'style' => 'margin-right: 10px'
        ]
    ) : '';

$set_type = ($elemento->tipo_riferimento == 'id_mas_rubrica' && $model->contatto_type != 'utl_utente') ?
    Html::a(
        ($model->use_type == 2) ? 'Imposta per messaggistica' : 'Imposta per allertamento', 
        ['/mas/set-use-type', 
            'id_riferimento'=>$model->id_riferimento, 
            'tipo_riferimento'=>$model->tipo_riferimento, 
            'id_contatto'=>$model->id_contatto,
            'contatto_type'=>$model->contatto_type,
            'value' => ($model->use_type == 2) ? 0 : 2
        ], 
        [
            'data-confirm' => 'Sicuro di voler confermare questa operazione?',
            'data-method' => 'post',
            'class' => 'btn btn-warning btn-sm',
            'style' => 'margin-right: 10px'
        ]
    ) : '';


echo "<div style=\"\">
<b style=\"line-height: 35px;\">" . $model->tipo() . ":</b> 
<span style=\"\">" . $model->valore_contatto . "</span><br />" .$btn." ".$set_predefinito." ".$set_mobile." ".$set_type."</div>";

?>