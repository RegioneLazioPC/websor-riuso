<?php

use yii\db\Migration;

/**
 * Class m190118_171251_alter_view_rubrica
 */
class m190118_171251_alter_view_rubrica extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_rubrica as
            SELECT 
            utl_contatto.contatto as valore_contatto,
            utl_contatto.id as id_contatto,
            utl_contatto.check_mobile as check_mobile,
            utl_contatto.check_predefinito as check_predefinito,
            'utl_contatto' as contatto_type,
            vol_organizzazione.denominazione as valore_riferimento,
            utl_contatto.type as tipo_contatto,
            'organizzazione' as tipologia_riferimento,
            vol_sede.lat as lat,
            vol_sede.lon as lon,
            vol_sede.geom as geom,
            vol_organizzazione.id as id_riferimento,
            'id_organizzazione' as tipo_riferimento,
            -1 as id_anagrafica,
            CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
            loc_comune.comune as comune,
            loc_provincia.sigla as provincia,
            '' as vendor
            FROM vol_organizzazione
            LEFT JOIN con_organizzazione_contatto ON con_organizzazione_contatto.id_organizzazione = vol_organizzazione.id
            LEFT JOIN utl_contatto ON utl_contatto.id = con_organizzazione_contatto.id_contatto
            LEFT JOIN vol_sede on vol_sede.id_organizzazione = vol_organizzazione.id and vol_sede.tipo = 'Sede Legale'
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            where utl_contatto.contatto is not null
            UNION
            SELECT 
            utl_contatto.contatto as valore_contatto,
            utl_contatto.id as id_contatto,
            utl_contatto.check_mobile as check_mobile,
            utl_contatto.check_predefinito as check_predefinito,
            'utl_contatto' as contatto_type,
            CONCAT(vol_sede.name, ' ', vol_sede.tipo) as valore_riferimento,
            utl_contatto.type as tipo_contatto,
            'sede' as tipologia_riferimento,
            vol_sede.lat as lat,
            vol_sede.lon as lon,
            vol_sede.geom as geom,
            vol_sede.id as id_riferimento,
            'id_sede' as tipo_riferimento,
            -1 as id_anagrafica,
            CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
            loc_comune.comune as comune,
            loc_provincia.sigla as provincia,
            '' as vendor
            FROM vol_sede
            LEFT JOIN con_sede_contatto ON con_sede_contatto.id_sede = vol_sede.id
            LEFT JOIN utl_contatto ON utl_contatto.id = con_sede_contatto.id_contatto
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            where utl_contatto.contatto is not null
            UNION
            SELECT 
            utl_contatto.contatto as valore_contatto,
            utl_contatto.id as id_contatto,
            utl_contatto.check_mobile as check_mobile,
            utl_contatto.check_predefinito as check_predefinito,
            'utl_contatto' as contatto_type,
            CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
            utl_contatto.type as tipo_contatto,
            'rappresentante legale' as tipologia_riferimento,
            vol_sede.lat as lat,
            vol_sede.lon as lon,
            vol_sede.geom as geom,
            vol_organizzazione.id as id_riferimento,
            'id_organizzazione_rappresentante_legale' as tipo_riferimento,
            utl_anagrafica.id as id_anagrafica,
            CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
            loc_comune.comune as comune,
            loc_provincia.sigla as provincia,
            '' as vendor
            FROM vol_organizzazione
            LEFT JOIN utl_anagrafica on utl_anagrafica.codfiscale = vol_organizzazione.cf_rappresentante_legale
            LEFT JOIN con_anagrafica_contatto ON con_anagrafica_contatto.id_anagrafica = utl_anagrafica.id
            LEFT JOIN utl_contatto ON utl_contatto.id = con_anagrafica_contatto.id_contatto
            LEFT JOIN vol_sede on vol_sede.id_organizzazione = vol_organizzazione.id and vol_sede.tipo = 'Sede Legale'
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            where utl_contatto.contatto is not null and utl_anagrafica.id is not null
            UNION
            SELECT 
            utl_contatto.contatto as valore_contatto,
            utl_contatto.id as id_contatto,
            utl_contatto.check_mobile as check_mobile,
            utl_contatto.check_predefinito as check_predefinito,
            'utl_contatto' as contatto_type,
            CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
            utl_contatto.type as tipo_contatto,
            'referente' as tipologia_riferimento,
            vol_sede.lat as lat,
            vol_sede.lon as lon,
            vol_sede.geom as geom,
            vol_organizzazione.id as id_riferimento,
            'id_organizzazione_referente' as tipo_riferimento,
            utl_anagrafica.id as id_anagrafica,
            CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
            loc_comune.comune as comune,
            loc_provincia.sigla as provincia,
            '' as vendor
            FROM vol_organizzazione
            LEFT JOIN utl_anagrafica on utl_anagrafica.codfiscale = vol_organizzazione.cf_referente
            LEFT JOIN con_anagrafica_contatto ON con_anagrafica_contatto.id_anagrafica = utl_anagrafica.id
            LEFT JOIN utl_contatto ON utl_contatto.id = con_anagrafica_contatto.id_contatto
            LEFT JOIN vol_sede on vol_sede.id_organizzazione = vol_organizzazione.id and vol_sede.tipo = 'Sede Legale'
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            where utl_contatto.contatto is not null and utl_anagrafica.id is not null
            UNION
            SELECT 
            utl_contatto.contatto as valore_contatto,
            utl_contatto.id as id_contatto,
            utl_contatto.check_mobile as check_mobile,
            utl_contatto.check_predefinito as check_predefinito,
            'utl_contatto' as contatto_type,
            CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
            utl_contatto.type as tipo_contatto,
            'volontario' as tipologia_riferimento,
            vol_sede.lat as lat,
            vol_sede.lon as lon,
            vol_sede.geom as geom,
            vol_volontario.id as id_riferimento,
            'id_volontario' as tipo_riferimento,
            utl_anagrafica.id as id_anagrafica,
            CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
            loc_comune.comune as comune,
            loc_provincia.sigla as provincia,
            '' as vendor
            FROM utl_anagrafica
            LEFT JOIN vol_volontario on vol_volontario.id_anagrafica = utl_anagrafica.id
            LEFT JOIN con_volontario_contatto ON con_volontario_contatto.id_volontario = vol_volontario.id
            LEFT JOIN utl_contatto ON utl_contatto.id = con_volontario_contatto.id_contatto
            LEFT JOIN vol_sede on vol_sede.id = vol_volontario.id_sede
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            where utl_contatto.contatto is not null and vol_volontario.id is not null
            union
            SELECT 
                utl_contatto.contatto as valore_contatto, 
                utl_contatto.id as id_contatto,
                utl_contatto.check_mobile as check_mobile,
                utl_contatto.check_predefinito as check_predefinito,
                'utl_contatto' as contatto_type,
                CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
                utl_contatto.type as tipo_contatto, 
                mas_rubrica.ruolo as tipologia_riferimento,
                mas_rubrica.lat as lat,
                mas_rubrica.lon as lon,
                mas_rubrica.geom as geom,
                mas_rubrica.id as id_riferimento,
                'id_mas_rubrica' as tipo_riferimento,
                utl_anagrafica.id as id_anagrafica,
                CONCAT(utl_indirizzo.indirizzo,' ',utl_indirizzo.civico,' ',utl_indirizzo.cap) as indirizzo,
                loc_comune.comune as comune,
                loc_provincia.sigla as provincia,
                '' as vendor
                FROM mas_rubrica
            LEFT JOIN utl_anagrafica on utl_anagrafica.id = mas_rubrica.id_anagrafica
            LEFT JOIN con_mas_rubrica_contatto ON con_mas_rubrica_contatto.id_mas_rubrica = mas_rubrica.id
            LEFT JOIN utl_contatto ON con_mas_rubrica_contatto.id_contatto = utl_contatto.id
            LEFT JOIN utl_indirizzo ON utl_indirizzo.id = mas_rubrica.id_indirizzo
            LEFT JOIN loc_comune on loc_comune.id = utl_indirizzo.id_comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            WHERE utl_contatto.id is not null
            UNION
            SELECT 
                utl_utente.device_token as valore_contatto, 
                utl_utente.id as id_contatto,
                0 as check_mobile,
                1 as check_predefinito,
                'utl_utente' as contatto_type,
                CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
                6 as tipo_contatto, 
                'volontario' as tipologia_riferimento,
                vol_sede.lat as lat,
                vol_sede.lon as lon,
                vol_sede.geom as geom,
                vol_volontario.id as id_riferimento,
                'id_volontario' as tipo_riferimento,
                utl_anagrafica.id as id_anagrafica,
                CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
                loc_comune.comune as comune,
                loc_provincia.sigla as provincia,
                CASE
                   WHEN (utl_utente.device_vendor IS NULL) THEN ''
                   ELSE utl_utente.device_vendor::text
                END AS vendor
                FROM utl_utente
            LEFT JOIN vol_volontario on vol_volontario.id_anagrafica = utl_utente.id_anagrafica AND vol_volontario.operativo = true
            LEFT JOIN utl_anagrafica on utl_anagrafica.id = utl_utente.id_anagrafica
            LEFT JOIN vol_sede on vol_sede.id = vol_volontario.id_sede
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            WHERE utl_utente.device_token is not null and vol_volontario.id is not null
            union
            SELECT 
                utl_utente.device_token as valore_contatto, 
                utl_utente.id as id_contatto,
                0 as check_mobile,
                1 as check_predefinito,
                'utl_utente' as contatto_type,
                CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
                6 as tipo_contatto, 
                'rappresentante legale' as tipologia_riferimento,
                vol_sede.lat as lat,
                vol_sede.lon as lon,
                vol_sede.geom as geom,
                vol_organizzazione.id as id_riferimento,
                'id_organizzazione_rappresentante_legale' as tipo_riferimento,
                utl_anagrafica.id as id_anagrafica,
                CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
                loc_comune.comune as comune,
                loc_provincia.sigla as provincia,
                CASE
                   WHEN (utl_utente.device_vendor IS NULL) THEN ''
                   ELSE utl_utente.device_vendor::text
                END AS vendor
                FROM utl_utente
            LEFT JOIN utl_anagrafica on utl_anagrafica.id = utl_utente.id_anagrafica
            LEFT JOIN vol_organizzazione on vol_organizzazione.cf_rappresentante_legale = utl_anagrafica.codfiscale
            LEFT JOIN vol_sede on vol_sede.id_organizzazione = vol_organizzazione.id and vol_sede.tipo = 'Sede Legale'
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            WHERE utl_utente.device_token is not null and vol_organizzazione.id is not null
            union
            SELECT 
                utl_utente.device_token as valore_contatto, 
                utl_utente.id as id_contatto,
                0 as check_mobile,
                1 as check_predefinito,
                'utl_utente' as contatto_type,
                CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
                6 as tipo_contatto, 
                'referente' as tipologia_riferimento,
                vol_sede.lat as lat,
                vol_sede.lon as lon,
                vol_sede.geom as geom,
                vol_organizzazione.id as id_riferimento,
                'id_organizzazione_referente' as tipo_riferimento,
                utl_anagrafica.id as id_anagrafica,
                CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
                loc_comune.comune as comune,
                loc_provincia.sigla as provincia,
                CASE
                   WHEN (utl_utente.device_vendor IS NULL) THEN ''
                   ELSE utl_utente.device_vendor::text
                END AS vendor
                FROM utl_utente
            LEFT JOIN utl_anagrafica on utl_anagrafica.id = utl_utente.id_anagrafica
            LEFT JOIN vol_organizzazione on vol_organizzazione.cf_referente = utl_anagrafica.codfiscale
            LEFT JOIN vol_sede on vol_sede.id_organizzazione = vol_organizzazione.id and vol_sede.tipo = 'Sede Legale'
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            WHERE utl_utente.device_token is not null and vol_organizzazione.id is not null
            union
            SELECT 
                utl_utente.device_token as valore_contatto, 
                utl_utente.id as id_contatto,
                0 as check_mobile,
                1 as check_predefinito,
                'utl_utente' as contatto_type,
                CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
                6 as tipo_contatto, 
                mas_rubrica.ruolo as tipologia_riferimento,
                mas_rubrica.lat as lat,
                mas_rubrica.lon as lon,
                mas_rubrica.geom as geom,
                utl_utente.id as id_riferimento,
                'id_utl_utente' as tipo_riferimento,
                utl_anagrafica.id as id_anagrafica,
                CONCAT(utl_indirizzo.indirizzo,' ',utl_indirizzo.civico,' ',utl_indirizzo.cap) as indirizzo,
                loc_comune.comune as comune,
                loc_provincia.sigla as provincia,
                CASE
                   WHEN (utl_utente.device_vendor IS NULL) THEN ''
                   ELSE utl_utente.device_vendor::text
                END AS vendor
                FROM utl_utente
            LEFT JOIN mas_rubrica ON mas_rubrica.id_anagrafica = utl_utente.id_anagrafica
            LEFT JOIN utl_anagrafica on utl_anagrafica.id = utl_utente.id_anagrafica
            LEFT JOIN utl_indirizzo ON utl_indirizzo.id = mas_rubrica.id_indirizzo
            LEFT JOIN loc_comune on loc_comune.id = utl_indirizzo.id_comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            WHERE utl_utente.device_token is not null and mas_rubrica.id is not null;"
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Yii::$app->db->createCommand("DROP VIEW IF EXISTS view_rubrica")->execute();

        Yii::$app->db->createCommand("CREATE VIEW view_rubrica as
            SELECT 
            utl_contatto.contatto as valore_contatto,
            utl_contatto.id as id_contatto,
            utl_contatto.check_mobile as check_mobile,
            utl_contatto.check_predefinito as check_predefinito,
            'utl_contatto' as contatto_type,
            vol_organizzazione.denominazione as valore_riferimento,
            utl_contatto.type as tipo_contatto,
            'organizzazione' as tipologia_riferimento,
            vol_sede.lat as lat,
            vol_sede.lon as lon,
            vol_sede.geom as geom,
            vol_organizzazione.id as id_riferimento,
            'id_organizzazione' as tipo_riferimento,
            -1 as id_anagrafica,
            CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
            loc_comune.comune as comune,
            loc_provincia.sigla as provincia
            FROM vol_organizzazione
            LEFT JOIN con_organizzazione_contatto ON con_organizzazione_contatto.id_organizzazione = vol_organizzazione.id
            LEFT JOIN utl_contatto ON utl_contatto.id = con_organizzazione_contatto.id_contatto
            LEFT JOIN vol_sede on vol_sede.id_organizzazione = vol_organizzazione.id and vol_sede.tipo = 'Sede Legale'
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            where utl_contatto.contatto is not null
            UNION
            SELECT 
            utl_contatto.contatto as valore_contatto,
            utl_contatto.id as id_contatto,
            utl_contatto.check_mobile as check_mobile,
            utl_contatto.check_predefinito as check_predefinito,
            'utl_contatto' as contatto_type,
            CONCAT(vol_sede.name, ' ', vol_sede.tipo) as valore_riferimento,
            utl_contatto.type as tipo_contatto,
            'sede' as tipologia_riferimento,
            vol_sede.lat as lat,
            vol_sede.lon as lon,
            vol_sede.geom as geom,
            vol_sede.id as id_riferimento,
            'id_sede' as tipo_riferimento,
            -1 as id_anagrafica,
            CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
            loc_comune.comune as comune,
            loc_provincia.sigla as provincia
            FROM vol_sede
            LEFT JOIN con_sede_contatto ON con_sede_contatto.id_sede = vol_sede.id
            LEFT JOIN utl_contatto ON utl_contatto.id = con_sede_contatto.id_contatto
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            where utl_contatto.contatto is not null
            UNION
            SELECT 
            utl_contatto.contatto as valore_contatto,
            utl_contatto.id as id_contatto,
            utl_contatto.check_mobile as check_mobile,
            utl_contatto.check_predefinito as check_predefinito,
            'utl_contatto' as contatto_type,
            CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
            utl_contatto.type as tipo_contatto,
            'rappresentante legale' as tipologia_riferimento,
            vol_sede.lat as lat,
            vol_sede.lon as lon,
            vol_sede.geom as geom,
            vol_organizzazione.id as id_riferimento,
            'id_organizzazione_rappresentante_legale' as tipo_riferimento,
            utl_anagrafica.id as id_anagrafica,
            CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
            loc_comune.comune as comune,
            loc_provincia.sigla as provincia
            FROM vol_organizzazione
            LEFT JOIN utl_anagrafica on utl_anagrafica.codfiscale = vol_organizzazione.cf_rappresentante_legale
            LEFT JOIN con_anagrafica_contatto ON con_anagrafica_contatto.id_anagrafica = utl_anagrafica.id
            LEFT JOIN utl_contatto ON utl_contatto.id = con_anagrafica_contatto.id_contatto
            LEFT JOIN vol_sede on vol_sede.id_organizzazione = vol_organizzazione.id and vol_sede.tipo = 'Sede Legale'
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            where utl_contatto.contatto is not null and utl_anagrafica.id is not null
            UNION
            SELECT 
            utl_contatto.contatto as valore_contatto,
            utl_contatto.id as id_contatto,
            utl_contatto.check_mobile as check_mobile,
            utl_contatto.check_predefinito as check_predefinito,
            'utl_contatto' as contatto_type,
            CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
            utl_contatto.type as tipo_contatto,
            'referente' as tipologia_riferimento,
            vol_sede.lat as lat,
            vol_sede.lon as lon,
            vol_sede.geom as geom,
            vol_organizzazione.id as id_riferimento,
            'id_organizzazione_referente' as tipo_riferimento,
            utl_anagrafica.id as id_anagrafica,
            CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
            loc_comune.comune as comune,
            loc_provincia.sigla as provincia
            FROM vol_organizzazione
            LEFT JOIN utl_anagrafica on utl_anagrafica.codfiscale = vol_organizzazione.cf_referente
            LEFT JOIN con_anagrafica_contatto ON con_anagrafica_contatto.id_anagrafica = utl_anagrafica.id
            LEFT JOIN utl_contatto ON utl_contatto.id = con_anagrafica_contatto.id_contatto
            LEFT JOIN vol_sede on vol_sede.id_organizzazione = vol_organizzazione.id and vol_sede.tipo = 'Sede Legale'
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            where utl_contatto.contatto is not null and utl_anagrafica.id is not null
            UNION
            SELECT 
            utl_contatto.contatto as valore_contatto,
            utl_contatto.id as id_contatto,
            utl_contatto.check_mobile as check_mobile,
            utl_contatto.check_predefinito as check_predefinito,
            'utl_contatto' as contatto_type,
            CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
            utl_contatto.type as tipo_contatto,
            'volontario' as tipologia_riferimento,
            vol_sede.lat as lat,
            vol_sede.lon as lon,
            vol_sede.geom as geom,
            vol_volontario.id as id_riferimento,
            'id_volontario' as tipo_riferimento,
            utl_anagrafica.id as id_anagrafica,
            CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
            loc_comune.comune as comune,
            loc_provincia.sigla as provincia
            FROM utl_anagrafica
            LEFT JOIN vol_volontario on vol_volontario.id_anagrafica = utl_anagrafica.id
            LEFT JOIN con_volontario_contatto ON con_volontario_contatto.id_volontario = vol_volontario.id
            LEFT JOIN utl_contatto ON utl_contatto.id = con_volontario_contatto.id_contatto
            LEFT JOIN vol_sede on vol_sede.id = vol_volontario.id_sede
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            where utl_contatto.contatto is not null and vol_volontario.id is not null
            union
            SELECT 
                utl_contatto.contatto as valore_contatto, 
                utl_contatto.id as id_contatto,
                utl_contatto.check_mobile as check_mobile,
                utl_contatto.check_predefinito as check_predefinito,
                'utl_contatto' as contatto_type,
                CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
                utl_contatto.type as tipo_contatto, 
                mas_rubrica.ruolo as tipologia_riferimento,
                mas_rubrica.lat as lat,
                mas_rubrica.lon as lon,
                mas_rubrica.geom as geom,
                mas_rubrica.id as id_riferimento,
                'id_mas_rubrica' as tipo_riferimento,
                utl_anagrafica.id as id_anagrafica,
                CONCAT(utl_indirizzo.indirizzo,' ',utl_indirizzo.civico,' ',utl_indirizzo.cap) as indirizzo,
                loc_comune.comune as comune,
                loc_provincia.sigla as provincia
                FROM mas_rubrica
            LEFT JOIN utl_anagrafica on utl_anagrafica.id = mas_rubrica.id_anagrafica
            LEFT JOIN con_mas_rubrica_contatto ON con_mas_rubrica_contatto.id_mas_rubrica = mas_rubrica.id
            LEFT JOIN utl_contatto ON con_mas_rubrica_contatto.id_contatto = utl_contatto.id
            LEFT JOIN utl_indirizzo ON utl_indirizzo.id = mas_rubrica.id_indirizzo
            LEFT JOIN loc_comune on loc_comune.id = utl_indirizzo.id_comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            WHERE utl_contatto.id is not null
            UNION
            SELECT 
                utl_utente.device_token as valore_contatto, 
                utl_utente.id as id_contatto,
                0 as check_mobile,
                1 as check_predefinito,
                'utl_utente' as contatto_type,
                CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
                6 as tipo_contatto, 
                'volontario' as tipologia_riferimento,
                vol_sede.lat as lat,
                vol_sede.lon as lon,
                vol_sede.geom as geom,
                vol_volontario.id as id_riferimento,
                'id_volontario' as tipo_riferimento,
                utl_anagrafica.id as id_anagrafica,
                CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
                loc_comune.comune as comune,
                loc_provincia.sigla as provincia
                FROM utl_utente
            LEFT JOIN vol_volontario on vol_volontario.id_anagrafica = utl_utente.id_anagrafica AND vol_volontario.operativo = true
            LEFT JOIN utl_anagrafica on utl_anagrafica.id = utl_utente.id_anagrafica
            LEFT JOIN vol_sede on vol_sede.id = vol_volontario.id_sede
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            WHERE utl_utente.device_token is not null and vol_volontario.id is not null
            union
            SELECT 
                utl_utente.device_token as valore_contatto, 
                utl_utente.id as id_contatto,
                0 as check_mobile,
                1 as check_predefinito,
                'utl_utente' as contatto_type,
                CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
                6 as tipo_contatto, 
                'rappresentante legale' as tipologia_riferimento,
                vol_sede.lat as lat,
                vol_sede.lon as lon,
                vol_sede.geom as geom,
                vol_organizzazione.id as id_riferimento,
                'id_organizzazione_rappresentante_legale' as tipo_riferimento,
                utl_anagrafica.id as id_anagrafica,
                CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
                loc_comune.comune as comune,
                loc_provincia.sigla as provincia
                FROM utl_utente
            LEFT JOIN utl_anagrafica on utl_anagrafica.id = utl_utente.id_anagrafica
            LEFT JOIN vol_organizzazione on vol_organizzazione.cf_rappresentante_legale = utl_anagrafica.codfiscale
            LEFT JOIN vol_sede on vol_sede.id_organizzazione = vol_organizzazione.id and vol_sede.tipo = 'Sede Legale'
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            WHERE utl_utente.device_token is not null and vol_organizzazione.id is not null
            union
            SELECT 
                utl_utente.device_token as valore_contatto, 
                utl_utente.id as id_contatto,
                0 as check_mobile,
                1 as check_predefinito,
                'utl_utente' as contatto_type,
                CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
                6 as tipo_contatto, 
                'referente' as tipologia_riferimento,
                vol_sede.lat as lat,
                vol_sede.lon as lon,
                vol_sede.geom as geom,
                vol_organizzazione.id as id_riferimento,
                'id_organizzazione_referente' as tipo_riferimento,
                utl_anagrafica.id as id_anagrafica,
                CONCAT(vol_sede.indirizzo,' ',vol_sede.cap) as indirizzo,
                loc_comune.comune as comune,
                loc_provincia.sigla as provincia
                FROM utl_utente
            LEFT JOIN utl_anagrafica on utl_anagrafica.id = utl_utente.id_anagrafica
            LEFT JOIN vol_organizzazione on vol_organizzazione.cf_referente = utl_anagrafica.codfiscale
            LEFT JOIN vol_sede on vol_sede.id_organizzazione = vol_organizzazione.id and vol_sede.tipo = 'Sede Legale'
            LEFT JOIN loc_comune on loc_comune.id = vol_sede.comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            WHERE utl_utente.device_token is not null and vol_organizzazione.id is not null
            union
            SELECT 
                utl_utente.device_token as valore_contatto, 
                utl_utente.id as id_contatto,
                0 as check_mobile,
                1 as check_predefinito,
                'utl_utente' as contatto_type,
                CONCAT(utl_anagrafica.nome, ' ', utl_anagrafica.cognome) as valore_riferimento,
                6 as tipo_contatto, 
                mas_rubrica.ruolo as tipologia_riferimento,
                mas_rubrica.lat as lat,
                mas_rubrica.lon as lon,
                mas_rubrica.geom as geom,
                utl_utente.id as id_riferimento,
                'id_utl_utente' as tipo_riferimento,
                utl_anagrafica.id as id_anagrafica,
                CONCAT(utl_indirizzo.indirizzo,' ',utl_indirizzo.civico,' ',utl_indirizzo.cap) as indirizzo,
                loc_comune.comune as comune,
                loc_provincia.sigla as provincia
                FROM utl_utente
            LEFT JOIN mas_rubrica ON mas_rubrica.id_anagrafica = utl_utente.id_anagrafica
            LEFT JOIN utl_anagrafica on utl_anagrafica.id = utl_utente.id_anagrafica
            LEFT JOIN utl_indirizzo ON utl_indirizzo.id = mas_rubrica.id_indirizzo
            LEFT JOIN loc_comune on loc_comune.id = utl_indirizzo.id_comune
            LEFT JOIN loc_provincia on loc_provincia.id = loc_comune.id_provincia
            WHERE utl_utente.device_token is not null and mas_rubrica.id is not null;"
        )->execute();
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190118_171251_alter_view_rubrica cannot be reverted.\n";

        return false;
    }
    */
}
