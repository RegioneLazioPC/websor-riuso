<?php

use yii\db\Migration;

/**
 * Class m211221_120035_create_view_for_profili_app
 */
class m211221_120035_create_view_for_profili_app extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("CREATE VIEW view_utenti_app AS 
            SELECT
                u.id as id_user,
                ut.id as id_utl_utente,
                a.id as id_anagrafica,
                v.id as id_volontario,
                u.username,
                u.email,
                a.nome,
                a.cognome,
                a.codfiscale,
                a.data_nascita, 
                a.matricola,
                v.operativo,
                v.ruolo,
                ut.tipo as tipo_utente,
                ut.codice_attivazione,
                vo.id as id_organizzazione,
                vo.denominazione as denominazione_organizzazione,
                CASE 
                    WHEN vo.cf_rappresentante_legale = a.codfiscale THEN 1 
                    ELSE 0 
                END rappresentante_legale,
                'VOLONTARIO' as tipo_utente_app
            FROM
            vol_volontario v
            LEFT JOIN utl_anagrafica a ON a.id = v.id_anagrafica
            LEFT JOIN vol_organizzazione vo ON vo.id = v.id_organizzazione
            LEFT JOIN utl_utente ut ON ut.id_anagrafica = a.id
            LEFT JOIN \"user\" u ON u.id = ut.iduser
            WHERE operativo is TRUE
            UNION ALL
            SELECT
                u.id as id_user,
                ut.id as id_utl_utente,
                a.id as id_anagrafica,
                null as id_volontario,
                u.username,
                u.email,
                a.nome,
                a.cognome,
                a.codfiscale,
                a.data_nascita, 
                a.matricola,
                false as operativo,
                rs.descrizione as ruolo,
                ut.tipo as tipo_utente,
                ut.codice_attivazione,
                null as id_organizzazione,
                null as denominazione_organizzazione,
                0 as rappresentante_legale,
                'ENTE' as tipo_utente_app
            FROM
            utl_utente ut
            LEFT JOIN utl_anagrafica a ON a.id = ut.id_anagrafica
            LEFT JOIN \"user\" u ON u.id = ut.iduser
            LEFT JOIN utl_ruolo_segnalatore rs ON rs.id = ut.id_ruolo_segnalatore
            WHERE (ut.codice_attivazione is not null OR ut.iduser is not null) AND ut.tipo = 2
            UNION ALL
            SELECT
                u.id as id_user,
                ut.id as id_utl_utente,
                a.id as id_anagrafica,
                null as id_volontario,
                u.username,
                u.email,
                a.nome,
                a.cognome,
                a.codfiscale,
                a.data_nascita, 
                a.matricola,
                false as operativo,
                'operatore' as ruolo,
                ut.tipo as tipo_utente,
                ut.codice_attivazione,
                null as id_organizzazione,
                null as denominazione_organizzazione,
                0 as rappresentante_legale,
                'OPERATORE PC' as tipo_utente_app
            FROM
            utl_operatore_pc opc
            LEFT JOIN utl_anagrafica a ON a.id = opc.id_anagrafica
            LEFT JOIN \"user\" u ON u.id = opc.iduser
            LEFT JOIN utl_utente ut ON ut.iduser = opc.iduser AND ut.tipo = 4;
            ")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_utenti_app")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211221_120035_create_view_for_profili_app cannot be reverted.\n";

        return false;
    }
    */
}
