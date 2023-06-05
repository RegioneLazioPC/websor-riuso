<?php

use yii\db\Migration;

/**
 * Class m220110_160632_alter_view_utenti_app
 */
class m220110_160632_alter_view_utenti_app extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW view_utenti_app")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_utenti_app AS 
             SELECT u.id AS id_user,
            ut.id AS id_utl_utente,
            a.id AS id_anagrafica,
            v.id AS id_volontario,
            u.username,
            u.status AS user_status,
            u.email,
            a.nome,
            a.cognome,
            a.codfiscale,
            a.data_nascita,
            a.matricola,
            v.operativo,
            v.ruolo,
            ut.tipo AS tipo_utente,
            ut.codice_attivazione,
            vo.id AS id_organizzazione,
            vo.denominazione AS denominazione_organizzazione,
                CASE
                    WHEN ((vo.cf_rappresentante_legale)::text = (a.codfiscale)::text) THEN 1
                    ELSE 0
                END AS rappresentante_legale,
            'VOLONTARIO'::text AS tipo_utente_app
           FROM ((((vol_volontario v
             LEFT JOIN utl_anagrafica a ON ((a.id = v.id_anagrafica)))
             LEFT JOIN vol_organizzazione vo ON ((vo.id = v.id_organizzazione)))
             LEFT JOIN utl_utente ut ON ((ut.id_anagrafica = a.id)))
             LEFT JOIN \"user\" u ON ((u.id = ut.iduser)))
          WHERE (v.operativo IS TRUE)
        UNION ALL
         SELECT u.id AS id_user,
            ut.id AS id_utl_utente,
            a.id AS id_anagrafica,
            NULL::integer AS id_volontario,
            u.username,
            u.status AS user_status,
            u.email,
            a.nome,
            a.cognome,
            a.codfiscale,
            a.data_nascita,
            a.matricola,
            false AS operativo,
            rs.descrizione AS ruolo,
            ut.tipo AS tipo_utente,
            ut.codice_attivazione,
            NULL::integer AS id_organizzazione,
            NULL::character varying AS denominazione_organizzazione,
            0 AS rappresentante_legale,
            'ENTE'::text AS tipo_utente_app
           FROM (((utl_utente ut
             LEFT JOIN utl_anagrafica a ON ((a.id = ut.id_anagrafica)))
             LEFT JOIN \"user\" u ON ((u.id = ut.iduser)))
             LEFT JOIN utl_ruolo_segnalatore rs ON ((rs.id = ut.id_ruolo_segnalatore)))
          WHERE (((ut.codice_attivazione IS NOT NULL) OR (ut.iduser IS NOT NULL)) AND (ut.tipo = 2))
        UNION ALL
         SELECT u.id AS id_user,
            ut.id AS id_utl_utente,
            a.id AS id_anagrafica,
            NULL::integer AS id_volontario,
            u.username,
            u.status AS user_status,
            u.email,
            a.nome,
            a.cognome,
            a.codfiscale,
            a.data_nascita,
            a.matricola,
            false AS operativo,
            'operatore'::character varying AS ruolo,
            ut.tipo AS tipo_utente,
            ut.codice_attivazione,
            NULL::integer AS id_organizzazione,
            NULL::character varying AS denominazione_organizzazione,
            0 AS rappresentante_legale,
            'OPERATORE PC'::text AS tipo_utente_app
           FROM (((utl_operatore_pc opc
             LEFT JOIN utl_anagrafica a ON ((a.id = opc.id_anagrafica)))
             LEFT JOIN \"user\" u ON ((u.id = opc.iduser)))
             LEFT JOIN utl_utente ut ON (((ut.iduser = opc.iduser) AND (ut.tipo in (0,4) ))));")->execute();
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW view_utenti_app")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_utenti_app AS 
             SELECT u.id AS id_user,
    ut.id AS id_utl_utente,
    a.id AS id_anagrafica,
    v.id AS id_volontario,
    u.username,
    u.status AS user_status,
    u.email,
    a.nome,
    a.cognome,
    a.codfiscale,
    a.data_nascita,
    a.matricola,
    v.operativo,
    v.ruolo,
    ut.tipo AS tipo_utente,
    ut.codice_attivazione,
    vo.id AS id_organizzazione,
    vo.denominazione AS denominazione_organizzazione,
        CASE
            WHEN ((vo.cf_rappresentante_legale)::text = (a.codfiscale)::text) THEN 1
            ELSE 0
        END AS rappresentante_legale,
    'VOLONTARIO'::text AS tipo_utente_app
   FROM ((((vol_volontario v
     LEFT JOIN utl_anagrafica a ON ((a.id = v.id_anagrafica)))
     LEFT JOIN vol_organizzazione vo ON ((vo.id = v.id_organizzazione)))
     LEFT JOIN utl_utente ut ON ((ut.id_anagrafica = a.id)))
     LEFT JOIN \"user\" u ON ((u.id = ut.iduser)))
  WHERE (v.operativo IS TRUE)
UNION ALL
 SELECT u.id AS id_user,
    ut.id AS id_utl_utente,
    a.id AS id_anagrafica,
    NULL::integer AS id_volontario,
    u.username,
    u.status AS user_status,
    u.email,
    a.nome,
    a.cognome,
    a.codfiscale,
    a.data_nascita,
    a.matricola,
    false AS operativo,
    rs.descrizione AS ruolo,
    ut.tipo AS tipo_utente,
    ut.codice_attivazione,
    NULL::integer AS id_organizzazione,
    NULL::character varying AS denominazione_organizzazione,
    0 AS rappresentante_legale,
    'ENTE'::text AS tipo_utente_app
   FROM (((utl_utente ut
     LEFT JOIN utl_anagrafica a ON ((a.id = ut.id_anagrafica)))
     LEFT JOIN \"user\" u ON ((u.id = ut.iduser)))
     LEFT JOIN utl_ruolo_segnalatore rs ON ((rs.id = ut.id_ruolo_segnalatore)))
  WHERE (((ut.codice_attivazione IS NOT NULL) OR (ut.iduser IS NOT NULL)) AND (ut.tipo = 2))
UNION ALL
 SELECT u.id AS id_user,
    ut.id AS id_utl_utente,
    a.id AS id_anagrafica,
    NULL::integer AS id_volontario,
    u.username,
    u.status AS user_status,
    u.email,
    a.nome,
    a.cognome,
    a.codfiscale,
    a.data_nascita,
    a.matricola,
    false AS operativo,
    'operatore'::character varying AS ruolo,
    ut.tipo AS tipo_utente,
    ut.codice_attivazione,
    NULL::integer AS id_organizzazione,
    NULL::character varying AS denominazione_organizzazione,
    0 AS rappresentante_legale,
    'OPERATORE PC'::text AS tipo_utente_app
   FROM (((utl_operatore_pc opc
     LEFT JOIN utl_anagrafica a ON ((a.id = opc.id_anagrafica)))
     LEFT JOIN \"user\" u ON ((u.id = opc.iduser)))
     LEFT JOIN utl_utente ut ON (((ut.iduser = opc.iduser) AND (ut.tipo = 4))));")->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220110_160632_alter_view_utenti_app cannot be reverted.\n";

        return false;
    }
    */
}
