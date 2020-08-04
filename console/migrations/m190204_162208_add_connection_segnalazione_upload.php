<?php

use yii\db\Migration;

/**
 * Class m190204_162208_add_connection_segnalazione_upload
 */
class m190204_162208_add_connection_segnalazione_upload extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('con_upl_media_utl_segnalazione', [
            'id' => $this->primaryKey(),
            'id_segnalazione' => $this->integer(),
            'id_media' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-upl_media_utl_segnalazione_segnalazione',
            'con_upl_media_utl_segnalazione',
            'id_segnalazione',
            'utl_segnalazione',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-upl_media_utl_segnalazione_media',
            'con_upl_media_utl_segnalazione',
            'id_media',
            'upl_media',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-upl_media_utl_segnalazione_segnalazione',
            'con_upl_media_utl_segnalazione'
        );

        $this->dropForeignKey(
            'fk-upl_media_utl_segnalazione_media',
            'con_upl_media_utl_segnalazione'
        );

        $this->dropTable('con_upl_media_utl_segnalazione');
    }

}
