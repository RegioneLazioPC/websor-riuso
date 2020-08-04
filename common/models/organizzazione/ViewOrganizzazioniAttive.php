<?php

namespace common\models\organizzazione;

use Yii;

class ViewOrganizzazioniAttive extends \yii\db\ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'view_organizzazioni_attive';
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ["id"];
    }

}
