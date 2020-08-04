<?php

use yii\db\Migration;

use common\models\UplTipoMedia;
/**
 * Class m190108_150457_add_tipo_media_editor
 */
class m190108_150457_add_tipo_media_editor extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $t = new UplTipoMedia;
        $t->descrizione = 'Immagine editor';
        $t->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $t = UplTipoMedia::find()->where(['descrizione'=>'Immagine editor'])->one();
        if($t) $t->delete();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190108_150457_add_tipo_media_editor cannot be reverted.\n";

        return false;
    }
    */
}
