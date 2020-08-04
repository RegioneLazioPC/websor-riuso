<?php

use yii\db\Migration;

/**
 * Class m180404_090330_add_foreign_key_table
 */
class m180404_090330_add_foreign_key_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addForeignKey(
            'fk_cala1',
            'alm_allerta_meteo',
            'cala1',
            'alm_tipo_allerta',
            'id',
            'SET NULL'
        );
        $this->addForeignKey(
            'fk_cala2',
            'alm_allerta_meteo',
            'cala2',
            'alm_tipo_allerta',
            'id',
            'SET NULL'
        );
        $this->addForeignKey(
            'fk_cala3',
            'alm_allerta_meteo',
            'cala3',
            'alm_tipo_allerta',
            'id',
            'SET NULL'
        );
        $this->addForeignKey(
            'fk_cala4',
            'alm_allerta_meteo',
            'cala4',
            'alm_tipo_allerta',
            'id',
            'SET NULL'
        );
        $this->addForeignKey(
            'fk_cala5',
            'alm_allerta_meteo',
            'cala5',
            'alm_tipo_allerta',
            'id',
            'SET NULL'
        );
        $this->addForeignKey(
            'fk_cala6',
            'alm_allerta_meteo',
            'cala6',
            'alm_tipo_allerta',
            'id',
            'SET NULL'
        );
        $this->addForeignKey(
            'fk_cala7',
            'alm_allerta_meteo',
            'cala7',
            'alm_tipo_allerta',
            'id',
            'SET NULL'
        );
        $this->addForeignKey(
            'fk_cala8',
            'alm_allerta_meteo',
            'cala8',
            'alm_tipo_allerta',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_livello_criticita_idro',
            'alm_allerta_meteo',
            'livello_criticita_idro',
            'alm_tipo_allerta',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'con_evento_extra_ibfk_1',
            'con_evento_extra',
            'idextra',
            'utl_extra_segnalazione',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'con_evento_extra_ibfk_2',
            'con_evento_extra',
            'idevento',
            'utl_evento',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_idevento',
            'con_evento_segnalazione',
            'idevento',
            'utl_evento',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_idsegnalazione',
            'con_evento_segnalazione',
            'idsegnalazione',
            'utl_segnalazione',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_id_evento',
            'con_operatore_evento',
            'idevento',
            'utl_evento',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_id_operatore',
            'con_operatore_evento',
            'idoperatore',
            'utl_operatore_pc',
            'id',
            'CASCADE'
        );


        $this->addForeignKey(
            'con_operatore_task_ibfk_1',
            'con_operatore_task',
            'idfunzione_supporto',
            'utl_funzioni_supporto',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'con_operatore_task_ibfk_2',
            'con_operatore_task',
            'idoperatore',
            'utl_operatore_pc',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'con_operatore_task_ibfk_3',
            'con_operatore_task',
            'idtask',
            'utl_task',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'con_operatore_task_ibfk_4',
            'con_operatore_task',
            'idevento',
            'utl_evento',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'con_operatore_task_ibfk_5',
            'con_operatore_task',
            'idsquadra',
            'utl_squadra_operativa',
            'id',
            'CASCADE'
        );


        $this->addForeignKey(
            'con_segnalazione_extra_ibfk_1',
            'con_segnalazione_extra',
            'idextra',
            'utl_extra_segnalazione',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'con_segnalazione_extra_ibfk_2',
            'con_segnalazione_extra',
            'idsegnalazione',
            'utl_segnalazione',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'con_utente_extra_ibfk_1',
            'con_utente_extra',
            'idutente',
            'utl_utente',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'con_utente_extra_ibfk_2',
            'con_utente_extra',
            'idextra',
            'utl_extra_segnalazione',
            'id',
            'CASCADE'
        );


        $this->addForeignKey(
            'utl_squadra_ibfk_2',
            'utl_automezzo',
            'idsquadra',
            'utl_squadra_operativa',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_tipologia_evento',
            'utl_evento',
            'tipologia_evento',
            'utl_tipologia',
            'id',
            'CASCADE'
        );


        $this->addForeignKey(
            'utl_operatore_pc_ibfk_1',
            'utl_operatore_pc',
            'idsalaoperativa',
            'utl_sala_operativa',
            'id',
            'SET NULL'
        );
        $this->addForeignKey(
            'utl_operatore_pc_ibfk_2',
            'utl_operatore_pc',
            'iduser',
            'user',
            'id',
            'SET NULL'
        );


        $this->addForeignKey(
            'fk_id_comune',
            'utl_segnalazione',
            'idcomune',
            'loc_comune',
            'id',
            'NO ACTION'
        );
        $this->addForeignKey(
            'fk_id_tipologia',
            'utl_segnalazione',
            'tipologia_evento',
            'utl_tipologia',
            'id',
            'NO ACTION'
        );
        $this->addForeignKey(
            'fk_id_utente',
            'utl_segnalazione',
            'idutente',
            'utl_utente',
            'id',
            'SET NULL'
        );


        $this->addForeignKey(
            'utl_segnalazione_attachments_ibfk_1',
            'utl_segnalazione_attachments',
            'idsegnalazione',
            'utl_segnalazione',
            'id',
            'CASCADE'
        );
        
        $this->addForeignKey(
            'fk_comune_id',
            'utl_squadra_operativa',
            'idcomune',
            'loc_comune',
            'id',
            'CASCADE'
        );


        $this->addForeignKey(
            'fk_id_user',
            'utl_utente',
            'iduser',
            'user',
            'id',
            'CASCADE'
        );


        $this->addForeignKey(
            'fk_tipo_organizzazione',
            'vol_organizzazione',
            'id_tipo_organizzazione',
            'vol_tipo_organizzazione',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk_id_organizzazione',
            'vol_sede',
            'id_organizzazione',
            'vol_organizzazione',
            'id',
            'SET NULL'
        );

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk_cala1',
            'alm_allerta_meteo');
        $this->dropForeignKey(
            'fk_cala2',
            'alm_allerta_meteo');
        $this->dropForeignKey(
            'fk_cala3',
            'alm_allerta_meteo');
        $this->dropForeignKey(
            'fk_cala4',
            'alm_allerta_meteo');
        $this->dropForeignKey(
            'fk_cala5',
            'alm_allerta_meteo');
        $this->dropForeignKey(
            'fk_cala6',
            'alm_allerta_meteo');
        $this->dropForeignKey(
            'fk_cala7',
            'alm_allerta_meteo');
        $this->dropForeignKey(
            'fk_cala8',
            'alm_allerta_meteo');

        $this->dropForeignKey(
            'fk_livello_criticita_idro',
            'alm_allerta_meteo');

        $this->dropForeignKey(
            'con_evento_extra_ibfk_1',
            'con_evento_extra');

        $this->dropForeignKey(
            'con_evento_extra_ibfk_2',
            'con_evento_extra');

        $this->dropForeignKey(
            'fk_idevento',
            'con_evento_segnalazione');
        $this->dropForeignKey(
            'fk_idsegnalazione',
            'con_evento_segnalazione');

        $this->dropForeignKey(
            'fk_id_evento',
            'con_operatore_evento');
        $this->dropForeignKey(
            'fk_id_operatore',
            'con_operatore_evento');


        $this->dropForeignKey(
            'con_operatore_task_ibfk_1',
            'con_operatore_task');
        $this->dropForeignKey(
            'con_operatore_task_ibfk_2',
            'con_operatore_task');
        $this->dropForeignKey(
            'con_operatore_task_ibfk_3',
            'con_operatore_task');
        $this->dropForeignKey(
            'con_operatore_task_ibfk_4',
            'con_operatore_task');
        $this->dropForeignKey(
            'con_operatore_task_ibfk_5',
            'con_operatore_task');


        $this->dropForeignKey(
            'con_segnalazione_extra_ibfk_1',
            'con_segnalazione_extra');
        $this->dropForeignKey(
            'con_segnalazione_extra_ibfk_2',
            'con_segnalazione_extra');

        $this->dropForeignKey(
            'con_utente_extra_ibfk_1',
            'con_utente_extra');
        $this->dropForeignKey(
            'con_utente_extra_ibfk_2',
            'con_utente_extra');


        $this->dropForeignKey(
            'utl_squadra_ibfk_2',
            'utl_automezzo');

        $this->dropForeignKey(
            'fk_tipologia_evento',
            'utl_evento');


        $this->dropForeignKey(
            'utl_operatore_pc_ibfk_1',
            'utl_operatore_pc');
        $this->dropForeignKey(
            'utl_operatore_pc_ibfk_2',
            'utl_operatore_pc');


        $this->dropForeignKey(
            'fk_id_comune',
            'utl_segnalazione');
        $this->dropForeignKey(
            'fk_id_tipologia',
            'utl_segnalazione');
        $this->dropForeignKey(
            'fk_id_utente',
            'utl_segnalazione');


        $this->dropForeignKey(
            'utl_segnalazione_attachments_ibfk_1',
            'utl_segnalazione_attachments');
        
        $this->dropForeignKey(
            'fk_comune_id',
            'utl_squadra_operativa');


        $this->dropForeignKey(
            'fk_id_user',
            'utl_utente');


        $this->dropForeignKey(
            'fk_tipo_organizzazione',
            'vol_organizzazione');

        $this->dropForeignKey(
            'fk_id_organizzazione',
            'vol_sede');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180404_090330_add_foreign_key_table cannot be reverted.\n";

        return false;
    }
    */
}
