<?php 
namespace backend\events;
use Yii;
use common\models\UtlEvento;

use yii\db\Expression;

class EditedUtlEventoEvent{
    // public AND static
    public static function handleEdited($event_id)
    {
    	$evento = UtlEvento::findOne($event_id);
        if($evento) :
            $evento->dataora_modifica = new Expression('NOW()');
            $evento->save();
        endif;

    }
}