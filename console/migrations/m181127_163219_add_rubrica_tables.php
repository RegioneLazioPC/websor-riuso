<?php


use yii\db\Schema;
use yii\db\Migration;

use common\models\UplTipoMedia;
/**
 * Class m181127_163219_add_rubrica_tables
 */
class m181127_163219_add_rubrica_tables extends Migration
{
    private $tipi_media = ['Allerta meteo'];
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('alm_allerta_meteo', 'id_media', $this->integer());

        $this->createTable('upl_media', [
            'id' => $this->primaryKey(),
            'ext' => $this->string(),
            'mime_type' => $this->string(),
            'nome' => $this->string(),
            'id_tipo_media' => $this->integer(),
            'uploaded_by' => $this->integer(),
            'uploader_ip' => $this->string(),
            'date_upload' => $this->date(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);

        $this->createTable('upl_tipo_media', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string(),
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);

        $this->addForeignKey(
            'fk-allerta_meteo_media',
            'alm_allerta_meteo',
            'id_media',
            'upl_media',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-media_tipo_tipo_media',
            'upl_media',
            'id_tipo_media',
            'upl_tipo_media',
            'id',
            'SET NULL'
        );

        foreach ($this->tipi_media as $tipo) {
            $t = UplTipoMedia::find()->where(['descrizione'=>$tipo])->one();
            if(!$t) $t = new UplTipoMedia;
            $t->descrizione = $tipo;
            $t->save();
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach ($this->tipi_media as $tipo) {
            $t = UplTipoMedia::find()->where(['descrizione'=>$tipo])->one();
            if($t) $t->delete();
        }

        $this->dropForeignKey(
            'fk-allerta_meteo_media',
            'alm_allerta_meteo'
        );

        $this->dropForeignKey(
            'fk-media_tipo_tipo_media',
            'upl_media'
        );

        $this->dropColumn('alm_allerta_meteo', 'id_media');

        $this->dropTable('upl_tipo_media');
        $this->dropTable('upl_media');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181127_163219_add_rubrica_tables cannot be reverted.\n";

        return false;
    }
    */
}
