<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $tableSchema = Yii::$app->db->schema->getTableSchema('{{%user}}');

        if ($tableSchema === null) {
            $this->createTable('{{%user}}', [
                'id' => $this->primaryKey(),
                'username' => $this->string()->notNull()->unique(),
                'auth_key' => $this->string(32)->notNull(),
                'password_hash' => $this->string()->notNull(),
                'password_reset_token' => $this->string()->unique(),
                'email' => $this->string()->notNull()->unique(),

                'status' => $this->smallInteger()->notNull()->defaultValue(10),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
            ], $tableOptions);
        }

               
        
        $this->createTable('alm_allerta_meteo', [
            'id' => $this->primaryKey(),
            'protocollo' => $this->integer(),
            'num_documento' => $this->integer(),
            'cala1' => $this->integer(),
            'cala2' => $this->integer(),
            'cala3' => $this->integer(),
            'cala4' => $this->integer(),
            'cala5' => $this->integer(),
            'cala6' => $this->integer(),
            'cala7' => $this->integer(),
            'cala8' => $this->integer(),
            'data_allerta' => $this->timestamp()->notNull(),
            'messaggio' => $this->text(),
            'avviso_meteo' => $this->binary(),
            'avviso_idro' => $this->binary(),
            'livello_criticita_idro' => $this->integer(),
            'data_creazione' => $this->timestamp(),
            'data_aggiornamento' => $this->timestamp(),
        ]);

        $this->createTable('alm_con_zona_criticita', [
            'id' => $this->primaryKey(),
            'id_allerta' => $this->integer(),
            'id_criticita' => $this->integer(),
            'zona' => $this->integer(),
            'precipitazioni' => $this->binary(),
            'nevicate' => $this->binary(),
            'venti' => $this->binary(),
            'mareggiate' => $this->binary(),
            'temporali' => $this->integer(5),
            'idro' => $this->integer(5),
            'fasi_operative' => $this->integer(5)
        ]);

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE alm_criticita_tipo AS ENUM ('meteo','idro')")
            ->execute();
        }

        $this->createTable('alm_criticita', [
            'id' => $this->primaryKey(),
            'id_allerta' => $this->integer(),
            'data' => $this->timestamp(),
            'ora_inizio' => $this->time(),
            'descrizione' => $this->text(),
            'tipo' => "alm_criticita_tipo"
        ]);

        $this->createTable('alm_tipo_allerta', [
            'id' => $this->primaryKey(),
            'tipologia' => $this->string(255)
        ]);

        $this->createTable('con_evento_extra', [
            'id' => $this->primaryKey(),
            'idevento' => $this->integer(),
            'idextra' => $this->integer(),
            'numero' => $this->integer(5),
            'note' => $this->string(255),
            'numero_nuclei_familiari' => $this->integer(5),
            'numero_disabili' => $this->integer(5),
            'numero_sistemazione_parenti_amici' => $this->integer(5),
            'numero_sistemazione_strutture_ricettive' => $this->integer(5),
            'numero_sistemazione_area_ricovero' => $this->integer(5),
            'numero_persone_isolate' => $this->integer(5),
            'numero_utenze' => $this->integer(),
        ]);

        $this->createTable('con_evento_segnalazione', [
            'idevento' => $this->integer(),
            'idsegnalazione' => $this->integer()
        ]);
        

        $this->createTable('con_operatore_evento', [
            'id' => $this->primaryKey(),
            'idoperatore' => $this->integer(),
            'idevento' => $this->integer(),
            'dataora' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP'
            // timestamp NOT NULL DEFAULT now() //da provare
            // https://stackoverflow.com/questions/910763/how-to-set-a-postgresql-default-value-datestamp-like-yyyymm per formattazione
        ]);

        $this->createTable('con_operatore_task', [
            'id' => $this->primaryKey(),
            'idoperatore' => $this->integer(),
            'idevento' => $this->integer(),
            'idfunzione_supporto' => $this->integer(),
            'idsquadra' => $this->integer(),
            'idautomezzo' => $this->integer(),
            'idtask' => $this->integer(),
            'dataora' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'note' => $this->string(1000),
            'is_task' => $this->binary()
        ]);

        $this->createTable('con_segnalazione_extra', [
            'id' => $this->primaryKey(),
            'idsegnalazione' => $this->integer(),
            'idextra' => $this->integer(),
            'numero' => $this->integer(5),
            'note' => $this->string(255),
            'numero_nuclei_familiari' => $this->integer(5),
            'numero_disabili' => $this->integer(5),
            'numero_sistemazione_parenti_amici' => $this->integer(5),
            'numero_sistemazione_strutture_ricettive' => $this->integer(5),
            'numero_sistemazione_area_ricovero' => $this->integer(5),
            'numero_persone_isolate' => $this->integer(5),
            'numero_utenze' => $this->integer()
        ]);

        $this->createTable('con_utente_extra', [
            'idutente' => $this->integer(),
            'idextra' => $this->integer()            
        ]);
        

        $this->createTable('loc_comune', [
            'id' => $this->primaryKey(),
            'id_regione' => $this->integer(),
            'id_provincia' => $this->integer(),
            'comune' => $this->string(255),
            'idstat' => $this->integer(10),
            'zona_geografica' => $this->string(255),
            'codnuts2' => $this->string(255),
            'codnuts3' => $this->string(255),
            'codmetropoli' => $this->string(255),
            'codistat' => $this->string(255),
            'codcatasto' => $this->string(255),
            'provincia_sigla' => $this->string(255),
            'cap' => $this->string(255),
            'codregione' => $this->string(255),
            'isprovincia' => $this->string(255),
            'altitudine' => $this->string(255),
            'islitoraneo' => $this->string(255),
            'codmontano' => $this->string(255),
            'superficie' => $this->string(255),
            'popolazione2011' => $this->string(255),
            'prefisso_tel' => $this->string(255)
        ]);
        
        $this->createTable('loc_continente', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(7),
            'nome_en' => $this->string(9),
        ]);

        $this->createTable('loc_nazione', [
            'id' => $this->primaryKey(),
            'idcontinente' => $this->integer(1),
            'idarea' => $this->integer(2),
            'sigla' => $this->string(3),
            'nome' => $this->string(70),
            'nome_en' => $this->string(50),
        ]);

        $this->createTable('loc_provincia', [
            'id' => $this->primaryKey(),
            'id_regione' => $this->integer(),
            'provincia' => $this->string(255),
            'sigla' => $this->string(2),
            'codripartizione' => $this->string(255),
            'codnuts1' => $this->string(255),
            'zona_geografica' => $this->string(255),
            'codnuts2' => $this->string(255),
            'regione' => $this->string(255),
            'codmetropoli' => $this->string(255),
            'codnuts3' => $this->string(255)
        ]);

        $this->createTable('loc_regione', [
            'id' => $this->primaryKey(),
            'regione' => $this->string(255)
        ]);

        $this->createTable('utl_automezzo', [
            'id' => $this->primaryKey(),
            'targa' => $this->string(45),
            'data_immatricolazione' => $this->date(),
            'idsquadra' => $this->integer(),
            'classe' => $this->string(100),
            'sottoclasse' => $this->string(100),
            'modello' => $this->string(100)
        ]);

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE utl_evento_stato AS ENUM ('Preallarme','Allarme','Emergenza','Chiuso')")
            ->execute();
        }
        

        $this->createTable('utl_evento', [
            'id' => $this->primaryKey(),
            'tipologia_evento' => $this->integer(),
            'note' => $this->text(),
            'lat' => $this->double(11,5),
            'lon' => $this->double(11,5),
            'idcomune' => $this->integer(),
            'luogo' => $this->string(255),
            'direzione' => $this->string(255),
            'distanza' => $this->string(100),
            'pericolo' => $this->binary(),
            'feriti' => $this->binary(),
            'vittime' => $this->binary(),
            'interruzione_viabilita' => $this->binary(),
            'aiuto_segnalatore' => $this->binary(),
            'dataora_evento' => $this->timestamp(),
            'dataora_modifica' => $this->timestamp(),
            'stato'=> "utl_evento_stato",
            'num_protocollo' => $this->string(255),
            'indirizzo' => $this->string(255),
            'is_public' => $this->binary()
        ]);

        $this->createTable('utl_extra_segnalazione', [
            'id' => $this->primaryKey(),
            'voce' => $this->string(255),
            'parent_id' => $this->integer(),
            'order' => $this->integer(5),
            'show_numero' => $this->binary(),
            'show_note' => $this->binary(),
            'show_num_nuclei_familiari' => $this->binary(),
            'show_num_disabili' => $this->binary(),
            'show_num_sistemazione_parenti_amici' => $this->binary(),
            'show_num_sistemazione_strutture_ricettive' => $this->binary(),
            'show_num_sistemazione_area_ricovero' => $this->binary(),
            'show_num_persone_isolate' => $this->binary(),
            'show_num_utenze' => $this->string(255) // ???
        ]);

        /*
        $this->createTable('utl_extra_segnalazione_20170509', [
            'id' => $this->primaryKey(),
            'voce' => $this->string(255),
            'parent_id' => $this->integer()
        ]);*/

        $this->createTable('utl_extra_utente', [
            'id' => $this->primaryKey(),
            'voce' => $this->string(255),
            'parent_id' => $this->integer(),
            'order' => $this->integer(4)
        ]);

        $this->createTable('utl_funzioni_supporto', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string(255),
            'code' => $this->string(50)
        ]);

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE utl_operatore_pc_ruolo AS ENUM ('operatore','funzionario SOP','funzionario SOR')")
            ->execute();
        }
        $this->createTable('utl_operatore_pc', [
            'id' => $this->primaryKey(),
            'idsalaoperativa' => $this->integer(),
            'iduser' => $this->integer(),
            'nome' => $this->string(255),
            'cognome' => $this->string(255),
            'email' => $this->string(255),
            'matricola' => $this->string(255),
            'ruolo' => "utl_operatore_pc_ruolo",
            'username' => $this->string(255), // ???
            'password' => $this->string(255) // ???
        ]);


        /*$this->createTable('utl_operatore_pc_20170317', [
            'id' => $this->primaryKey(),
            'idsalaoperativa' => $this->integer(),
            'iduser' => $this->integer(),
            'nome' => $this->string(255),
            'cognome' => $this->string(255),
            'email' => $this->string(255),
            'matricola' => $this->string(255),
            'ruolo' => "utl_operatore_pc",
            'username' => $this->string(255), // ???
            'password' => $this->string(255) // ???
        ]);*/

        $this->createTable('utl_ruolo_segnalatore', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string(255)
        ]);

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE utl_sala_operativa_tipo AS ENUM ('SOR','SOP')")
            ->execute();
        }
        $this->createTable('utl_sala_operativa', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(255),
            'indirizzo' => $this->string(255),
            'comune' => $this->string(255), //???
            'tipo' => "utl_sala_operativa_tipo",
            'sigla_provincia' => $this->string(2) // ???
        ]);

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE utl_segnalazione_stato AS ENUM ('Nuova e assegnata al SOP','Verificata e trasformata in evento','Chiusa')")
            ->execute();
            Yii::$app->db->createCommand("CREATE TYPE utl_segnalazione_fonte AS ENUM ('App','Radio','Email','Telefono')")
            ->execute();
        }

        $this->createTable('utl_segnalazione', [
            'id' => $this->primaryKey(),
            'idutente' => $this->integer(),
            'idsalaoperativa' => $this->integer(),
            'foto' => $this->string(255), // ??? cloudinary soluzione non adatta
            'foto_locale' => $this->binary(),
            'tipologia_evento'=> $this->integer(),
            'note'=> $this->text(),
            'lat'=> $this->double(7,5), // ??? in altri punti è 11,5
            'lon'=> $this->double(7,5), // ???
            'idcomune' => $this->integer(),
            'indirizzo' => $this->string(255),
            'luogo' => $this->string(255),
            'direzione'=> $this->string(255),
            'distanza' => $this->string(255),
            'pericolo' => $this->binary(),
            'feriti' => $this->binary(),
            'vittime' => $this->binary(),
            'interruzione_viabilita' => $this->binary(),
            'aiuto_segnalatore' => $this->binary(),
            'dataora_segnalazione' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'stato' => "utl_segnalazione_stato",
            'fonte' => "utl_segnalazione_fonte",
            'num_protocollo' => $this->string(255)
        ]);

        $this->createTable('utl_segnalazione_attachments', [
            'id' => $this->primaryKey(),
            'idsegnalazione' => $this->integer()->notNull(),
            'filename' => $this->string(255),
            'date' => $this->datetime(), // ??? in alcuni data
        ]);


        $this->createTable('utl_squadra_operativa', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(255)->notNull(),
            'caposquadra' => $this->string(255)->notNull(),
            'idcomune' => $this->integer(5),
            'numero_membri' => $this->integer(5),
            'tel_caposquadra' => $this->string(255), // ??? perchè non salvare anagrafiche capisquadra
            'cell_caposquadra' => $this->string(255),
            'frequenza_trans' => $this->string(255),
            'frequenza_ric' => $this->string(255)
        ]);

        $this->createTable('utl_task', [
            'id' => $this->primaryKey(),
            'descrizione' => $this->string(255),
            'code' => $this->string(50)
        ]);

        $this->createTable('utl_tipologia', [
            'id' => $this->primaryKey(),
            'tipologia' => $this->string(255)
        ]);

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE utl_utente_device_vendor AS ENUM ('ios','android')")
            ->execute();
        }

        $this->createTable('utl_utente', [
            'id' => $this->primaryKey(),
            'iduser' => $this->integer(),
            'nome' => $this->string(255),
            'cognome' => $this->string(255),
            'codfiscale' => $this->string(255),
            'data_nascita' => $this->date(),
            'luogo_nascita' => $this->string(255),
            'comune_residenza' => $this->string(255), // ???
            'telefono' => $this->string(255),
            'email' => $this->string(255),
            'smscode' => $this->string(20),
            'sms_status' => $this->string(5),
            'tipo' => $this->integer(2),
            'id_tipo_ente_pubblico' => $this->integer(),
            'id_ruolo_segnalatore' => $this->integer(),
            'device_token' => $this->string(255),
            'device_vendor' => "utl_utente_device_vendor",
        ]);

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE vol_organizzazione_tipo_albo_regionale AS ENUM ('D.D.G.','D.D.S.','D.G.R.')")
            ->execute();
        }

        $this->createTable('vol_organizzazione', [
            'id' => $this->primaryKey(),
            'id_tipo_organizzazione' => $this->integer(),
            'denominazione' => $this->string(255),
            'codicefiscale' => $this->string(16), // ??? in altri punti è codfiscale
            'partita_iva' => $this->string(16),
            'tipo_albo_regionale' => "vol_organizzazione_tipo_albo_regionale",
            'num_albo_regionale' => $this->integer(),
            'data_albo_regionale' => $this->timestamp(),
            'num_albo_provinciale' => $this->integer(),
            'num_albo_nazionale' => $this->integer(),
            'num_assicurazione' => $this->integer(),
            'societa_assicurazione' => $this->string(1), // ??? 
            'data_scadenza_assicurazione' => $this->timestamp(),
            'note'=> $this->string(1) // ???
        ]);

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE vol_sede_tipo AS ENUM ('Sede Legale','Sede Operativa')")
            ->execute();
        }

        $this->createTable('vol_sede', [
            'id' => $this->primaryKey(),
            'id_organizzazione' => $this->integer(),
            'indirizzo' => $this->string(1), // ???
            'comune' => $this->integer(),
            'tipo' => "vol_sede_tipo",
            'email' => $this->string(255),
            'email_pec' => $this->string(255),
            'telefono' => $this->string(255),
            'cellulare' => $this->string(255),
            'altro_telefono' => $this->string(255),
            'fax'=> $this->string(255),
            'sitoweb'=> $this->string(255)
        ]);

        $this->createTable('vol_tipo_organizzazione', [
            'id' => $this->primaryKey(),
            'tipologia' => $this->string(1), // ???
        ]);

        if ($this->db->driverName === 'pgsql') {
            Yii::$app->db->createCommand("CREATE TYPE vol_volontario_ruolo AS ENUM ('Presidente','Vice Presidente','Volontario')")
            ->execute();
        }

        $this->createTable('vol_volontario', [
            'id' => $this->primaryKey(),
            'id_anagrafica' => $this->integer(),
            'ruolo' => "vol_volontario_ruolo",
            'operativo' => $this->binary(),
            'spec_principale' => $this->string(1),
            'valido_dal' => $this->timestamp(),
            'valido_al' => $this->timestamp()
        ]);

        // inizializzo le tabelle
        // escluse isp_ispezione, isp_soggetti_ispezione, con_ispezione_soggetti, isp_tipo_fenomeno, isp_report, richiesta_mezzo_aereo




    }

    public function down()
    {
        
        $this->dropTable('{{%user}}');

        $this->dropTable('alm_allerta_meteo');

        $this->dropTable('alm_con_zona_criticita');

        $this->dropTable('alm_criticita');

        $this->dropTable('alm_tipo_allerta');

        $this->dropTable('con_evento_extra');

        $this->dropTable('con_evento_segnalazione');
        

        $this->dropTable('con_operatore_evento');

        $this->dropTable('con_operatore_task');

        $this->dropTable('con_segnalazione_extra');

        $this->dropTable('con_utente_extra');
        

        $this->dropTable('loc_comune');
        
        $this->dropTable('loc_continente');

        $this->dropTable('loc_nazione');

        $this->dropTable('loc_provincia');

        $this->dropTable('loc_regione');

        $this->dropTable('utl_automezzo');

        $this->dropTable('utl_evento');

        $this->dropTable('utl_extra_segnalazione');

        // ???
        //$this->dropTable('utl_extra_segnalazione_20170509');

        $this->dropTable('utl_extra_utente');

        $this->dropTable('utl_funzioni_supporto');

        $this->dropTable('utl_operatore_pc');

        //$this->dropTable('utl_operatore_pc_20170317');

        $this->dropTable('utl_ruolo_segnalatore');

        $this->dropTable('utl_sala_operativa');

        $this->dropTable('utl_segnalazione');

        $this->dropTable('utl_segnalazione_attachments');


        $this->dropTable('utl_squadra_operativa');

        $this->dropTable('utl_task');

        $this->dropTable('utl_tipologia');

        $this->dropTable('utl_utente');

        $this->dropTable('vol_organizzazione');

        $this->dropTable('vol_sede');

        $this->dropTable('vol_tipo_organizzazione');

        $this->dropTable('vol_volontario');

        Yii::$app->db->createCommand("DROP TYPE alm_criticita_tipo")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE utl_evento_stato")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE utl_operatore_pc_ruolo")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE utl_sala_operativa_tipo")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE utl_segnalazione_stato")
            ->execute();
            Yii::$app->db->createCommand("DROP TYPE utl_segnalazione_fonte")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE utl_utente_device_vendor")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE vol_organizzazione_tipo_albo_regionale")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE vol_sede_tipo")
            ->execute();

        Yii::$app->db->createCommand("DROP TYPE vol_volontario_ruolo")
            ->execute();
    }
}
