<?php

namespace console\controllers;

use common\models\User;
use common\models\UtlOperatorePc;
use common\models\UtlRuoloSegnalatore;

use common\utils\gPoint; // da usare per convertire in utm
use common\utils\GpointConverter;

use PHPCoord\OSRef;
use PHPCoord\UTMRef;

use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;


use Exception;
use Yii;
use yii\console\Controller;


use ruskid\csvimporter\CSVImporter;
use ruskid\csvimporter\CSVReader;
use ruskid\csvimporter\MultipleImportStrategy;

use common\models\VolSede;
use common\models\VolOrganizzazione;
use common\models\VolTipoOrganizzazione;
use common\models\LocComune;
use common\models\UtlFunzioniSupporto;

use common\models\UtlAutomezzo;
use common\models\UtlAttrezzatura;
use common\models\UtlAutomezzoTipo;
use common\models\UtlAttrezzaturaTipo;
use common\models\UtlCategoriaAutomezzoAttrezzatura;

use common\models\UtlTipologia;
use common\models\VolVolontario;
use common\models\UtlAnagrafica;

use common\models\LocIndirizzo;
use common\models\LocCivico;

use yii\helpers\ArrayHelper;
use ZipArchive;

class InstallerController extends Controller
{

    public $username;
    public $password;
    public $email;
    public $with_operatore;
    public $operatore_role;

    public $nome;
    public $cognome;
    public $matricola;
    public $role;
    public $id_user;

    public $nord;
    public $est;

    public function options($actionID)
    {
        return [
            'username', 'password', 'email', 'with_operatore',
            'nome', 'cognome', 'matricola', 'role', 'id_user', 'operatore_role',
            'nord', 'est'
        ];
    }

    public function optionAliases()
    {
        return [
            'u' => 'username', 'p' => 'password', 'e' => 'email', 'wo' => 'with_operatore',
            'no' => 'nome', 'co' => 'cognome', 'mo' => 'matricola', 'ro' => 'role', 'iu' => 'id_user', 'opr' => 'operatore_role',
            'cn' => 'nord', 'ce' => 'est'
        ];
    }

    /**
     * Per inserimento utente admin
     *
     * options:
     * -u = username
     * -e = email
     * -p = password
     * -no = nome
     * -co = cognome
     * -opr = Ruolo come operatore
     * -ro = Ruolo applicazione
     * -mo = Matricola
     * -wo = (== 1 per inserire operatore)
     *
     * ./yii installer/addadmin -u="nomeutente" -e="nomeutente@mailinator.com" -p="password" -no="Nome" -co="Utente" -opr="Dirigente" -mo="RL_132194" -ro="Admin" -wo="1"
     * @return void
     */
    public function actionAddadmin()
    {
        $auth = Yii::$app->authManager;

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        if (!$user->save()) :
            print_r($user->getErrors());
        else :
            if ($this->role) :
                $role = $auth->getRole($this->role);
            else :
                $role = $auth->getRole('Dirigente');
            endif;
            $auth->assign($role, $user->id);
            if ($this->with_operatore == '1') :
                $operatore = new UtlOperatorePc();
                $anagrafica = new UtlAnagrafica();
                $anagrafica->nome = $this->nome;
                $anagrafica->cognome = $this->cognome;
                $anagrafica->email = $this->email;
                $anagrafica->matricola = $this->matricola;
                if (!$anagrafica->save()) :
                    print_r($anagrafica->getErrors());
                    return 1;
                endif;
                $operatore->id_anagrafica = $anagrafica->id;
                $operatore->iduser = $user->id;
                $operatore->ruolo = (!$this->operatore_role) ? $this->role : $this->operatore_role;
                if (!$operatore->save()) :
                    print_r($operatore->getErrors());
                    $user->delete();
                else :
                    echo "Operatore creato\n";
                endif;
            endif;
            echo "Utente creato\n";
        endif;
    }

    /**
     * Inserisci tipologie di default e icone
     * @return void
     */
    public function actionAddTipologieEvento()
    {
        // Execute sql
        $path = Yii::getAlias('@console');
        $sqlFile = $path . '/data/tipologie_evento/utl_tipologia.sql';
        $sql = file_get_contents($sqlFile);

        $pdo = Yii::$app->db->pdo;
        $pdo->beginTransaction();
        try {
            $pdo->exec($sql);
            $pdo->commit();
        } catch (\PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }

        // Unzip icons file
        $zip = new ZipArchive;
        if ($zip->open($path . '/data/tipologie_evento/icone_eventi.zip') === TRUE) {
            $pathBackendWeb = Yii::getAlias('@backend');
            $zip->extractTo($pathBackendWeb . '/web/images/');
            $zip->close();
            echo 'unzip ok';
        } else {
            echo 'unzip failed';
        }
    }

    /**
     * Inserisci un operatore
     * options:
     * -no = nome
     * -co = cognome
     * -opr = Ruolo come operatore
     * -ro = Ruolo applicazione
     * @return void
     */
    public function actionAddoperatore()
    {
        $operatore = new UtlOperatorePc();
        $anagrafica = new UtlAnagrafica();
        $anagrafica->nome = $this->nome;
        $anagrafica->cognome = $this->cognome;
        $anagrafica->matricola = $this->matricola;
        if (!$anagrafica->save()) :
            print_r($anagrafica->getErrors());
            return 1;
        endif;
        $operatore->id_anagrafica = $anagrafica->id;
        $operatore->iduser = $this->id_user;
        $operatore->ruolo = $this->role;
        if (!$operatore->save()) :
            print_r($operatore->getErrors());
        else :
            echo "Operatore creato\n";
        endif;
    }

    /**
     * Test conversione coordinate montemario to lat,lon
     * @return [type] [description]
     */
    public function actionConvert()
    {
        $point = new GpointConverter('WGS 84');


        $proj4 = new Proj4php();

        $proj3003    = new Proj('+proj=tmerc +lat_0=0 +lon_0=9 +k=0.9996 +x_0=1500000 +y_0=0 +ellps=intl +units=m +no_defs', $proj4);
        $proj3004    = new Proj('+proj=tmerc +lat_0=0 +lon_0=15 +k=0.9996 +x_0=2520000 +y_0=0 +ellps=intl +units=m +no_defs', $proj4);
        $projWGS84  = new Proj('EPSG:4326', $proj4);

        $pointSrc = new Point(1790993.09262451, 4642024.64358599, $proj3003);
        $pointDest = $proj4->transform($projWGS84, $pointSrc);
        echo "Conversion 3003: " . $pointDest->toShortString() . " in WGS84\n";

        $pointSrc = new Point(1790993.09262451, 4642024.64358599, $proj3004);
        $pointDest = $proj4->transform($projWGS84, $pointSrc);
        echo "Conversion 3004: " . $pointDest->toShortString() . " in WGS84\n";
        var_dump($pointDest->toArray());


        $proj4 = new Proj4php();
        $proj3003    = new Proj('+proj=tmerc +lat_0=0 +lon_0=9 +k=0.9996 +x_0=1500000 +y_0=0 +ellps=intl +units=m +no_defs', $proj4);
        $projWGS84  = new Proj('EPSG:4326', $proj4);

        $pointSrc = new Point(12.506452863782, 41.875058052322, $projWGS84);
        $pointDest = $proj4->transform($proj3003, $pointSrc);
        var_dump($pointDest->toArray());
    }

    /**
     * Importa organizzazioni da export xls
     * 
     * ./yii installer/import-organizzazioni
     * @return [type] [description]
     */
    public function actionImportOrganizzazioni()
    {
        // manca il tipo di sede
        // codicert -> lo salvo come "codicert - {value}" in note organizzazione
        // nome è identificativo
        // loc comune legge istatcom_cod (colonna G) trasforma in intero e nuovamente in stringa (associazione da usare)
        // localita ignoro
        // tipostr_descr (colonna J) -> utl_organizzazione_tipo
        // des dettaglio: (colonna R desc, colonna Q dettaglio) -> strtolower
        //  Telefono sede
        //  Telefono
        //  Email
        //  fax
        //  Fax
        //  Indirizzo internet -> sitoweb
        //  
        //  est (T) e nord (U) se null ignoro
        //  
        //  per ognuno cerco organizzazione con denominazione = nome o note = codicert - {codicert}
        //  se trova organizzazione cerca sede con indirizzo == attuale
        //  se trova sede aggiorna con nuovo dato contatti
        //  se non trova organizzazione aggiunge
        //  aggiunge sede
        //  
        $path = Yii::getAlias('@console');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path . '/data/ext_rubrica.xls');
        $reader->setReadDataOnly(true);


        $worksheet = $reader->load($path . '/data/ext_rubrica.xls');
        $rows = $worksheet->getActiveSheet()->toArray(null, false, true, false);

        $n = 0;
        $id_sedi = [];
        foreach ($rows as $row) {
            // per ogni riga inizia la logica
            if ($n > 0) {
                $org = VolOrganizzazione::find()
                    ->where(['denominazione' => $row[2]])
                    ->orWhere(['note' => 'codicert - ' . $row[1]])
                    ->orWhere(['ref_id' => $row[0]])
                    ->one();
                if (!$org) :
                    $org = new VolOrganizzazione();
                    $org->ref_id = $row[0];
                    $org->denominazione = $row[2];
                    $org->note = 'codicert - ' . $row[1];
                    $tipoOrg = VolTipoOrganizzazione::find()->where(['tipologia' => $row[9]])->one();
                    if (!$tipoOrg) :
                        $tipoOrg = new VolTipoOrganizzazione();
                        $tipoOrg->tipologia = $row[9];
                        $tipoOrg->save();
                    endif;
                    $org->id_tipo_organizzazione = $tipoOrg->id;
                    $org->save();
                endif;



                // cerca le sedi
                $sede = VolSede::find()
                    ->where(['indirizzo' => $row[12]])
                    ->andWhere(['id_organizzazione' => $org->id])
                    ->one();
                if (!$sede) :
                    $sede = new VolSede();
                endif;
                $sede->tipo = 'Sede Legale';
                $sede->id_organizzazione = $org->id;
                $sede->indirizzo = $row[12];
                $com_data = intval($row[6]);
                $comune = LocComune::find()->where(['codistat' => $com_data])->one();
                if ($comune) : $sede->comune = $comune->id;
                endif;

                $sede = $this->addDataToSede($row, $sede);
                $sede = $this->addCoordsToSede($row, $sede);
                $sede->id_organizzazione = $org->id;
                $sede->save();
                $id_sedi[] = $sede->id;
            }
            $n++;
        }
        $all_sedi = VolSede::find()->where(['id' => $id_sedi])->all();
        foreach ($all_sedi as $sede) {
            $new_sede = new VolSede();
            $new_sede->attributes = $sede->attributes;
            $new_sede->tipo = 'Sede Operativa';
            $new_sede->save();
        }
    }

    /**
     * Import automezzi e attrezzature da export xls
     * ./yii installer/import-automezzi-attrezzature
     * @return [type] [description]
     */
    public function actionImportAutomezziAttrezzature()
    {

        // prima mettiamo le categorie
        $mzaereo = UtlCategoriaAutomezzoAttrezzatura::find()
            ->where(['descrizione' => 'MEZZO AEREO'])->one();
        if (!$mzaereo) :
            $mzaereo = new UtlCategoriaAutomezzoAttrezzatura();
            $mzaereo->descrizione = 'MEZZO AEREO';
            $mzaereo->save();
        endif;

        $mzaib = UtlCategoriaAutomezzoAttrezzatura::find()
            ->where(['descrizione' => 'AIB'])->one();
        if (!$mzaib) :
            $mzaib = new UtlCategoriaAutomezzoAttrezzatura();
            $mzaib->descrizione = 'AIB';
            $mzaib->save();
        endif;

        echo "Apro file\n";
        $path = Yii::getAlias('@console');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path . '/data/exp_risorse.xls');
        $reader->setReadDataOnly(true);

        echo "Leggo file\n";
        $worksheet = $reader->load($path . '/data/exp_risorse.xls');
        $rows = $worksheet->getActiveSheet()->toArray(null, false, true, false);

        $n = 0;
        $id_sedi = [];
        //echo "Inizio loop file\n";
        foreach ($rows as $row) {
            if ($n > 0) :
                //echo "Inserisco riga\n";
                $org_name = str_replace("Possesso: ", "", $row[5]);
                $org = VolOrganizzazione::find()->where(['denominazione' => $org_name])->one();

                if ($org) :
                    $sede = VolSede::find()
                        ->where(['id_organizzazione' => $org->id])
                        ->andWhere(['tipo' => 'Sede Operativa'])
                        ->one();
                    if ($sede) :
                        // solo se la trova
                        switch ($row[2]) {
                            case 'ATTREZZATURE':
                                //echo 'attrezzatura';
                                $this->addAttrezzatura($sede, $row, $mzaereo, $mzaib);
                                break;
                            case 'MEZZI':
                                //echo 'mezzo';
                                $this->addAutomezzo($sede, $row, $mzaereo, $mzaib);
                                break;
                            default:
                                echo "Tipologia non riconosciuta\n";
                                break;
                        }
                    endif;
                endif;
                echo $n . "\n";
            endif;
            $n++;
        }
    }

    /**
     * Inserisci singolo automezzo
     * @param [object] $sede    VolSede
     * @param [array] $row     
     * @param [object] $mzaereo 
     * @param [object] $mzaib  
     */
    private function addAutomezzo($sede, $row, $mzaereo, $mzaib)
    {
        //echo "creo automezzo\n";
        $identificativo_risorsa = intval($row[0]);
        $automezzo = UtlAutomezzo::find()->where(['ref_id' => $identificativo_risorsa])->one();
        if (!$automezzo) $automezzo = new UtlAutomezzo();


        $automezzo->ref_id = $identificativo_risorsa;

        $automezzo->targa = "" . $row[15];
        $automezzo->modello = "" . $row[33];
        $automezzo->allestimento = "" . $row[67];

        $tipo = UtlAutomezzoTipo::find()->where(['descrizione' => $row[3]])->one();
        if (!$tipo) :
            $tipo = new UtlAutomezzoTipo();
            $tipo->descrizione = $row[3];
            if ($row[3] == 'ELICOTTERO' || $row[3] == 'MEZZO AEREO') $tipo->is_mezzo_aereo = true;
            $tipo->save();
        endif;

        $automezzo->idtipo = $tipo->id;
        $automezzo->idsede = $sede->id;
        $automezzo->idorganizzazione = $sede->id_organizzazione;

        if (!$automezzo->save()) {
            var_dump($automezzo->getErrors());
            return false;
        }

        return $automezzo->ref_id;
    }

    /**
     * Inserisci singola attrezzatura
     * @param [object] $sede    VolSede
     * @param [array] $row     
     * @param [object] $mzaereo 
     * @param [object] $mzaib   
     */
    private function addAttrezzatura($sede, $row, $mzaereo, $mzaib)
    {

        $identificativo_risorsa = intval($row[0]);
        $attrezzatura = UtlAttrezzatura::find()->where(['ref_id' => $identificativo_risorsa])->one();
        if (!$attrezzatura) $attrezzatura = new UtlAttrezzatura();
        $attrezzatura->ref_id = $identificativo_risorsa;
        $attrezzatura->modello = "" . $row[33];
        $attrezzatura->allestimento = "" . $row[67];

        $tipo = UtlAttrezzaturaTipo::find()->where(['descrizione' => $row[3]])->one();
        if (!$tipo) :
            $tipo = new UtlAttrezzaturaTipo();
            $tipo->descrizione = $row[3];
            $tipo->save();
        endif;
        $attrezzatura->idtipo = $tipo->id;
        $attrezzatura->idsede = $sede->id;
        $attrezzatura->idorganizzazione = $sede->id_organizzazione;

        if (!$attrezzatura->save()) {
            var_dump($attrezzatura->getErrors());
            return false;
        }

        return $attrezzatura->ref_id;
    }

    /**
     * Aggiungi informazioni su singola sede
     * @param [array] $row  
     * @param [object] $sede VolSede
     */
    private function addDataToSede($row, $sede)
    {
        switch (strtolower($row[17])) {
            case 'fax':
                $sede->fax = $row[16];
                break;
            case 'telefono sede':
                $sede->telefono = $row[16];
                break;
            case 'email':
                $sede->email = $row[16];
                break;
            case 'telefono':
                $sede->altro_telefono = $row[16];
                break;
            case 'indirizzo internet':
                $sede->sitoweb = $row[16];
                break;
        }
        return $sede;
    }

    /**
     * Inserisci coordinate sede
     * @param [array] $row  
     * @param [object] $sede VolSede
     */
    private function addCoordsToSede($row, $sede)
    {
        if (!isset($row[19]) || !isset($row[20]) || $row[19] == 'null' || $row[19] == '' || $row[20] == 'null' || $row[20] == '') return $sede;
        $proj4 = new Proj4php();

        // http://www.geoin.it/coordinate_converter/
        // http://spatialreference.org/ref/epsg/3004/
        // Create two different projections.
        $proj3003    = new Proj('+proj=tmerc +lat_0=0 +lon_0=9 +k=0.9996 +x_0=1500000 +y_0=0 +ellps=intl +units=m +no_defs', $proj4);
        $projWGS84  = new Proj('EPSG:4326', $proj4);

        $pointSrc = new Point($row[19], $row[20], $proj3003);
        $pointDest = $proj4->transform($projWGS84, $pointSrc);

        $converted = $pointDest->toArray();
        $sede->lat = $converted[1];
        $sede->lon = $converted[0];
        //$sede->geom = "ST_GeomFromText(POINT(".$converted[1]." ".$converted[0]."), 4326)";
        return $sede;
    }

    /**
     * Importa tutti i dati da file ExportZeroGis
     *
     * 1 - categorie -> sottocategorie eventi
     *     a partire da riga 2
     *     inserisci prima colonna come tipo evento
     *     per ogni colonna successiva finchè non trova stringa vuota inserisci sottotipo
     *
     * 2 - categoria evento -> tipo mezzi 
     *     non ha senso allo stato attuale e in base alle richieste esplicitate vincolare 
     *     associazioni di questo tipo, meglio un sistema aperto
     *
     * 3 - associazioni 1
     *     inserire dati per ogni associazione come organizzazione
     *     aggiungere sede legale e sede operativa con medesimi dati per ogni organizzazione
     *
     * 4 - associazioni 2
     *     per ogni riga cerca associazione, se non trovata la escludo dall'inserimento
     *     non avendo informazioni sulle sezioni non vengono considerate
     *     aggiungi i dati del referente
     *
     * 5 - mezzi 
     *     utilizzare script precedente modificando gli indici da 6 in poi con il successivo
     *
     * ./yii installer/import-all
     *
     * @deprecated
     * @return void
     */
    public function actionImportAll()
    {

        $path = Yii::getAlias('@console');
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path . '/data/Export_ZEROGIS.xlsx');
        $reader->setReadDataOnly(true);

        $worksheet = $reader->load($path . '/data/Export_ZEROGIS.xlsx');


        $worksheet->setActiveSheetIndex(3);
        $rows = $worksheet->getActiveSheet()->toArray(null, false, true, false);
        $this->addOrganizzazioni($rows);

        $worksheet->setActiveSheetIndex(4);
        $rows = $worksheet->getActiveSheet()->toArray(null, false, true, false);
        $this->addOtherOrganizzazioniInfo($rows);

        $worksheet->setActiveSheetIndex(5);
        $rows = $worksheet->getActiveSheet()->toArray(null, false, true, false);
        $this->addProductionMezziAttrezzature($rows);
    }

    /**
     * Inserisci tipi/sottotipi evento
     * @param [array] $rows 
     */
    private function addTipiSottotipi($rows)
    {
        $n_row = 0;
        foreach ($rows as $row) {
            if ($n_row > 0) {
                if ($row[0] && $row[0] != "") {

                    $tipo_evento = UtlTipologia::find()->where(['ilike', 'tipologia', trim($row[0])])->one();
                    if (!$tipo_evento) : $tipo_evento = new UtlTipologia();
                    endif;
                    $tipo_evento->tipologia = trim($row[0]);
                    $tipo_evento->save();

                    echo $row[0] . " -> inserito \n";

                    $row_col = 1;
                    while ($row[$row_col] && $row[$row_col] != "") {
                        $sottotipo_evento = UtlTipologia::find()
                            ->where(['ilike', 'tipologia', trim($row[$row_col])])
                            ->andWhere(['idparent' => $tipo_evento->id])
                            ->one();
                        if (!$sottotipo_evento) : $sottotipo_evento = new UtlTipologia();
                        endif;
                        $sottotipo_evento->tipologia = trim($row[$row_col]);
                        $sottotipo_evento->idparent = $tipo_evento->id;
                        $sottotipo_evento->save();
                        echo "  " . $row[$row_col] . " inserito \n";
                        $row_col++;
                    }
                }
            }
            $n_row++;
        }
    }

    /**
     * Inserisci organizzazioni primo foglio
     * @param [array] $rows 
     */
    private function addOrganizzazioni($rows)
    {
        $added = [];
        $n = 0;
        foreach ($rows as $row) {
            if ($n > 0 && $row[2] != '') {
                $org = VolOrganizzazione::find()
                    ->where(['denominazione' => $row[3]])
                    ->orWhere(['ref_id' => $row[0]])
                    ->one();
                if (!$org) :
                    $org = new VolOrganizzazione();
                    $org->ref_id = $row[0];
                    $org->denominazione = $row[3];
                    $org->note = ""; //'codicert - '.$row[1];
                    $tipoOrg = VolTipoOrganizzazione::find()->where(['tipologia' => $row[2]])->one();
                    if (!$tipoOrg) :
                        $tipoOrg = new VolTipoOrganizzazione();
                        $tipoOrg->tipologia = $row[2];
                        $tipoOrg->save();
                    endif;
                    $org->id_tipo_organizzazione = $tipoOrg->id;
                endif;
                $org->ref_id = intval($row[0]);
                $org->save();


                // cerca le sedi
                if ($row[4] && $row[4] != "") :
                    $sede = VolSede::find()
                        ->where(['indirizzo' => $row[4]])
                        ->andWhere(['id_organizzazione' => $org->id])
                        ->one();
                    if (!$sede) :
                        $sede = new VolSede();
                    endif;
                    $sede->tipo = 'Sede Legale';
                    $sede->id_organizzazione = $org->id;
                    $sede->indirizzo = $row[4];
                    $sede->cap = $row[6];
                    if ($row[5] && $row[5] != "") :
                        $comune = LocComune::find()
                            ->where(['comune' => $row[5]])
                            ->andWhere(['id_regione' => Yii::$app->params['region_filter_id']])
                            ->one();
                        //echo $comune;
                        if ($comune) :
                            $sede->comune = $comune->id;
                        else :
                            echo "Comune non trovato " . $row[5] . "\n";
                        endif;
                    endif;

                    $sede->fax = $row[15];
                    $sede->altro_fax = $row[14];
                    $sede->telefono = $row[13]; // telefono sede
                    $sede->altro_telefono = $row[12]; // telefono h24
                    $sede->email = $row[16];

                    $sede->lat = $row[11];
                    $sede->lon = $row[10];

                    $sede->coord_x = $row[9];
                    $sede->coord_y = $row[8];

                    $sede->id_organizzazione = $org->id;
                    $sede->save();
                endif;
                // per cancellazione
                if ($org->id) $added[] = $org->ref_id;
            }
            $n++;
        }


        $to_del = VolOrganizzazione::find()
            ->where(['not in', 'ref_id', $added])
            ->andWhere(['!=', 'ref_id', 0])
            ->andWhere('ref_id is not null')
            ->all();
        foreach ($to_del as $del_org) :
            $sede = VolSede::find()
                ->where(['id_organizzazione' => $del_org->id])
                ->all();


            if ($del_org->delete()) :
                echo "Cancellata organizzazione " . $del_org->denominazione . "\n";
                foreach ($sede as $s) {
                    if ($s->delete()) :
                        echo "Cancellata sede " . $s->indirizzo . "\n";
                    endif;
                }
            else :
                echo "errore eliminazione organizzazione\n";
            endif;
        endforeach;
    }

    /**
     * Inserisci organizzazioni secondo foglio
     * @param [array] $rows
     */
    private function addOtherOrganizzazioniInfo($rows)
    {

        $n = 0;
        $added = [];
        foreach ($rows as $row) {
            if ($n > 0 && $row[1] != '') {
                $org = VolOrganizzazione::find()->where(['denominazione' => $row[1]])->one();
                if ($org) :

                    $org->codicefiscale = $row[8];
                    $org->partita_iva = $row[8];

                    $dt = \DateTime::createFromFormat('d/m/Y', $row[9]);
                    if ($dt) :
                        $org->data_costituzione = $dt->format('Y-m-d');
                    endif;
                    $org->num_albo_regionale = $row[11];
                    $dt = \DateTime::createFromFormat('d/m/Y', $row[12]);
                    if ($dt) :
                        $org->data_albo_regionale = $dt->format('Y-m-d');
                    endif;
                    $org->save();

                    $anagrafica = UtlAnagrafica::find()
                        ->where(['codfiscale' => $row[22]])
                        ->one();
                    if (!$anagrafica) :
                        $anagrafica = new UtlAnagrafica();
                    endif;
                    $anagrafica->nome = $row[13];
                    $anagrafica->cognome = $row[14];
                    $dt = \DateTime::createFromFormat('d/m/Y', $row[16]);
                    if ($dt) :
                        $anagrafica->data_nascita = $dt->format('Y-m-d');
                    endif;
                    $anagrafica->indirizzo_residenza = $row[18] . " " . $row[19];
                    $anagrafica->codfiscale = $row[22];
                    $anagrafica->cap_residenza = $row[20];
                    $anagrafica->telefono = $row[23];
                    $anagrafica->pec = $row[25];
                    $anagrafica->save();

                    $org->tel_responsabile = $row[23];
                    $org->email_responsabile = $row[24];
                    $org->pec_responsabile = $row[25];
                    $org->nome_responsabile = $row[13] . " " . $row[14];

                    $org->tel_referente = $row[44];
                    $org->email_referente = $row[46];
                    $org->fax_referente = $row[45];
                    $org->nome_referente = $row[42] . " " . $row[43];

                    $org->save();

                    $volontario = VolVolontario::find()
                        ->where(['id_organizzazione' => $org->id])
                        ->andWhere(['id_anagrafica' => $anagrafica->id])
                        ->one();

                    if (!$volontario) :
                        $volontario = new VolVolontario();
                    endif;

                    $volontario->ruolo = 'Rappresentante Legale';
                    $volontario->id_organizzazione = $org->id;
                    $volontario->id_anagrafica = $anagrafica->id;
                    $volontario->save();

                    if ($org->id) $added[] = $org->ref_id;

                endif;
            }
            $n++;
        }

        $to_del = VolOrganizzazione::find()
            ->where(['not in', 'ref_id', $added])
            ->andWhere(['!=', 'ref_id', 0])
            ->andWhere('ref_id is not null')
            ->all();
        foreach ($to_del as $del_org) :
            $sede = VolSede::find()
                ->where(['id_organizzazione' => $del_org->id])
                ->all();

            $volontario = $volontario = VolVolontario::find()
                ->where(['id_organizzazione' => $del_org->id])
                ->all();

            if ($del_org->delete()) :
                echo "Cancellata organizzazione " . $del_org->denominazione . "\n";

                foreach ($sede as $s) {
                    if ($s->delete()) :
                        echo "Cancellata sede " . $s->indirizzo . "\n";
                    endif;
                }

                foreach ($volontario as $v) {
                    if ($v->delete()) :
                        echo "Cancellato volontario\n";
                    endif;
                }

            else :
                echo "errore eliminazione organizzazione\n";
            endif;


        endforeach;
    }

    /**
     * Inserisci mezzi attrezzature
     * @param [array] $rows 
     */
    private function addProductionMezziAttrezzature($rows)
    {

        $mezzi_refs = [];
        $attrz_refs = [];

        $n = 0;
        $id_sedi = [];

        foreach ($rows as $row) {
            if ($n > 0) :

                $org_name = str_replace("Possesso: ", "", $row[5]);
                $org = VolOrganizzazione::find()->where(['denominazione' => $org_name])->one();

                if ($org) :
                    $sede = VolSede::find()
                        ->where(['id_organizzazione' => $org->id])
                        ->one();
                    if ($sede) :
                        // solo se la trova
                        switch ($row[2]) {
                            case 'ATTREZZATURE':
                                //echo 'attrezzatura';
                                $add = $this->addAttrezzatura($sede, $row, null, null);
                                if ($add) : $attrz_refs[] = $add;
                                endif;
                                //echo "ref_id, attrezzatura: ".$add."\n";
                                break;
                            case 'MEZZI':
                                //echo 'mezzo';
                                $add = $this->addAutomezzo($sede, $row, null, null);
                                if ($add) : $mezzi_refs[] = $add;
                                endif;
                                //echo "ref_id, automezzo: ".$add."\n";
                                break;
                            default:
                                echo "Tipologia non riconosciuta\n";
                                break;
                        }
                    endif;
                endif;
                echo $n . "\n";
            endif;
            $n++;
        }



        $to_del_au = UtlAutomezzo::find()
            ->where(['not in', 'ref_id', $mezzi_refs])
            ->andWhere(['!=', 'ref_id', 0])
            ->andWhere('ref_id is not null')
            ->all();
        foreach ($to_del_au as $auto) {
            if ($auto->delete()) echo "Automezzo eliminato\n";
        }

        $to_del_at = UtlAttrezzatura::find()
            ->where(['not in', 'ref_id', $attrz_refs])
            ->andWhere(['!=', 'ref_id', 0])
            ->andWhere('ref_id is not null')
            ->all();
        foreach ($to_del_at as $attrz) {
            if ($attrz->delete()) echo "Attrezzatura eliminata\n";
        }
    }

    /**
     * Inserimento dati iniziali
     * ./yii installer/add-csv-data
     * 
     * @return void
     */
    public function actionAddCsvData()
    {
        $this->addContinents();
        $this->addNations();
        $this->addRegions();
        $this->addProvince();
        $this->addComuni();
        $this->addUtlSegnalazione();
        //$this->addRuoloSegnalatore();
        $this->addFunzioniSupporto();
        $this->addTasks();
    }

    /**
     * Inserisci continenti
     */
    private function addContinents()
    {
        $path = Yii::getAlias('@console');
        $importer = new CSVImporter;
        $importer->setData(new CSVReader([
            'filename' => $path . '/data/loc_continente.csv',
            'fgetcsvOptions' => [
                'delimiter' => ","
            ]
        ]));
        $tableName = 'loc_continente';
        $config = [
            [
                'attribute' => 'id',
                'value' => function ($line) {
                    return $line[0];
                },
                'unique' => true,
            ],
            [
                'attribute' => 'nome',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[1]);
                },
                'unique' => true,
            ],
            [
                'attribute' => 'nome_en',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[2]);
                },
                'unique' => true,
            ],
        ];
        $importer->import(new MultipleImportStrategy([
            'tableName' => $tableName,
            'configs' => $config,
        ]));
    }
    /**
     * Inserisci nazioni
     */
    private function addNations()
    {
        $path = Yii::getAlias('@console');
        $importer = new CSVImporter;
        $importer->setData(new CSVReader([
            'filename' => $path . '/data/loc_nazione.csv',
            'fgetcsvOptions' => [
                'delimiter' => ","
            ]
        ]));
        $tableName = 'loc_nazione';
        $config = [
            [
                'attribute' => 'id',
                'value' => function ($line) {
                    return $line[0];
                },
                'unique' => true,
            ],
            [
                'attribute' => 'idcontinente',
                'value' => function ($line) {
                    return $line[1];
                },
                'unique' => false,
            ],
            [
                'attribute' => 'idarea',
                'value' => function ($line) {
                    return 0;
                },
                'unique' => true,
            ],
            [
                'attribute' => 'sigla',
                'value' => function ($line) {
                    return $line[3];
                },
                'unique' => true,
            ],
            [
                'attribute' => 'nome',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[4]);
                },
                'unique' => true,
            ],
            [
                'attribute' => 'nome_en',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[5]);
                },
                'unique' => true,
            ],
        ];
        $importer->import(new MultipleImportStrategy([
            'tableName' => $tableName,
            'configs' => $config,
        ]));
    }
    /**
     * Inserisci Regioni
     */
    private function addRegions()
    {
        $path = Yii::getAlias('@console');
        $importer = new CSVImporter;
        $importer->setData(new CSVReader([
            'filename' => $path . '/data/loc_regione.csv',
            'fgetcsvOptions' => [
                'delimiter' => ","
            ]
        ]));
        $tableName = 'loc_regione';
        $config = [
            [
                'attribute' => 'id',
                'value' => function ($line) {
                    return $line[0];
                },
                'unique' => true,
            ],
            [
                'attribute' => 'regione',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[1]);
                },
                'unique' => true,
            ]
        ];
        $importer->import(new MultipleImportStrategy([
            'tableName' => $tableName,
            'configs' => $config,
        ]));
    }
    /**
     * Inserisci province
     */
    private function addProvince()
    {
        $path = Yii::getAlias('@console');
        $importer = new CSVImporter;
        $importer->setData(new CSVReader([
            'filename' => $path . '/data/loc_provincia.csv',
            'fgetcsvOptions' => [
                'delimiter' => ","
            ]
        ]));
        $tableName = 'loc_provincia';
        $config = [
            [
                'attribute' => 'id',
                'value' => function ($line) {
                    return $line[0];
                },
                'unique' => true,
            ],
            [
                'attribute' => 'id_regione',
                'value' => function ($line) {
                    return $line[1];
                },
                'unique' => false,
            ],
            [
                'attribute' => 'provincia',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[2]);
                },
                'unique' => true,
            ],
            [
                'attribute' => 'sigla',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[3]);
                },
                'unique' => true,
            ],
            [
                'attribute' => 'codripartizione',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[4]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'codnuts1',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[5]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'zona_geografica',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[6]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'codnuts2',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[7]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'regione',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[8]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'codmetropoli',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[9]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'codnuts3',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[10]);
                },
                'unique' => false,
            ]
        ];
        $importer->import(new MultipleImportStrategy([
            'tableName' => $tableName,
            'configs' => $config,
        ]));
    }
    /**
     * Inserisci comuni
     */
    private function addComuni()
    {
        $path = Yii::getAlias('@console');
        $importer = new CSVImporter;
        $importer->setData(new CSVReader([
            'filename' => $path . '/data/loc_comune.csv',
            'fgetcsvOptions' => [
                'delimiter' => ","
            ]
        ]));
        $tableName = 'loc_comune';
        $config = [
            [
                'attribute' => 'id',
                'value' => function ($line) {
                    return $line[0];
                },
                'unique' => true,
            ],
            [
                'attribute' => 'id_regione',
                'value' => function ($line) {
                    return $line[1];
                },
                'unique' => false,
            ],
            [
                'attribute' => 'id_provincia',
                'value' => function ($line) {
                    return $line[2];
                },
                'unique' => false,
            ],
            [
                'attribute' => 'comune',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[3]);
                },
                'unique' => true,
            ],
            [
                'attribute' => 'idstat',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[4]);
                },
                'unique' => true,
            ],
            [
                'attribute' => 'zona_geografica',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[5]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'codnuts2',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[6]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'codnuts3',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[7]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'codmetropoli',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[8]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'codistat',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[9]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'codcatasto',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[10]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'provincia_sigla',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[11]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'cap',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[12]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'codregione',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[13]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'isprovincia',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[14]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'zona_altimetrica',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[15]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'altitudine',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[16]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'islitoraneo',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[17]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'codmontano',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[18]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'superficie',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[19]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'popolazione2011',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[20]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'prefisso_tel',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[21]);
                },
                'unique' => false,
            ]
        ];
        $importer->import(new MultipleImportStrategy([
            'tableName' => $tableName,
            'configs' => $config,
        ]));
    }
    /**
     * Inserisci extra segnalazioni
     */
    private function addUtlSegnalazione()
    {
        $path = Yii::getAlias('@console');
        $importer = new CSVImporter;
        $importer->setData(new CSVReader([
            'filename' => $path . '/data/utl_extra_segnalazione.csv',
            'fgetcsvOptions' => [
                'delimiter' => ","
            ]
        ]));
        $tableName = 'utl_extra_segnalazione';
        $config = [
            [
                'attribute' => 'id',
                'value' => function ($line) {
                    return $line[0];
                },
                'unique' => true,
            ],
            [
                'attribute' => 'voce',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[1]);
                },
                'unique' => false,
            ],
            [
                'attribute' => 'parent_id',
                'value' => function ($line) {
                    return (isset($line[2]) && $line[2] != '') ? $line[2] : null;
                },
                'unique' => false,
            ],
            [
                'attribute' => 'order',
                'value' => function ($line) {
                    return (isset($line[3]) && $line[3] != '') ? $line[3] : 0;
                },
                'unique' => false,
            ],
            [
                'attribute' => 'show_numero',
                'value' => function ($line) {
                    return $line[4] || 0;
                },
                'unique' => false,
            ],
            [
                'attribute' => 'show_note',
                'value' => function ($line) {
                    return $line[5] || 0;
                },
                'unique' => false,
            ],
            [
                'attribute' => 'show_num_nuclei_familiari',
                'value' => function ($line) {
                    return $line[6] || 0;
                },
                'unique' => false,
            ],
            [
                'attribute' => 'show_num_disabili',
                'value' => function ($line) {
                    return $line[7] || 0;
                },
                'unique' => false,
            ],
            [
                'attribute' => 'show_num_sistemazione_parenti_amici',
                'value' => function ($line) {
                    return $line[8] || 0;
                },
                'unique' => false,
            ],
            [
                'attribute' => 'show_num_sistemazione_strutture_ricettive',
                'value' => function ($line) {
                    return $line[9] || 0;
                },
                'unique' => false,
            ],
            [
                'attribute' => 'show_num_sistemazione_area_ricovero',
                'value' => function ($line) {
                    return $line[10] || 0;
                },
                'unique' => false,
            ],
            [
                'attribute' => 'show_num_persone_isolate',
                'value' => function ($line) {
                    return $line[11] || 0;
                },
                'unique' => false,
            ],
            [
                'attribute' => 'show_num_utenze',
                'value' => function ($line) {
                    return $line[12] || 0;
                },
                'unique' => false,
            ]
        ];
        $importer->import(new MultipleImportStrategy([
            'tableName' => $tableName,
            'configs' => $config,
        ]));
    }

    /**
     * Inserisci extra ruoli segnalatori
     */
    private function addRuoloSegnalatore()
    {
        $roles = [
            'Sindaco',
            'Resp. UTC',
            'Resp. VVU',
            'Dirigente',
            'Resp. PC',
            'Comandante di stazione',
            'Responsabile di sala',
            'Prefetto',
            'Capo Gabinetto',
            'Presidente Associazione',
            'Dipendente',
            'Volontario',
            'Altro'
        ];
        foreach ($roles as $role) {
            $r = UtlRuoloSegnalatore::find()->where(['descrizione' => $role])->one();
            $r = ($r) ? $r : new UtlRuoloSegnalatore();
            $r->descrizione = $role;
            $r->save();
        }
    }
    /**
     * Inserisci funzioni supporto
     */
    private function addFunzioniSupporto()
    {
        $path = Yii::getAlias('@console');
        $importer = new CSVImporter;
        $importer->setData(new CSVReader([
            'filename' => $path . '/data/utl_funzioni_supporto.csv',
            'fgetcsvOptions' => [
                'delimiter' => ","
            ]
        ]));
        $tableName = 'utl_funzioni_supporto';
        $config = [
            [
                'attribute' => 'id',
                'value' => function ($line) {
                    return $line[0];
                },
                'unique' => true,
            ],
            [
                'attribute' => 'descrizione',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[1]);
                },
                'unique' => true,
            ]
        ];
        $importer->import(new MultipleImportStrategy([
            'tableName' => $tableName,
            'configs' => $config,
        ]));
    }
    /**
     * Inserisci task
     */
    private function addTasks()
    {
        $path = Yii::getAlias('@console');
        $importer = new CSVImporter;
        $importer->setData(new CSVReader([
            'filename' => $path . '/data/utl_task.csv',
            'fgetcsvOptions' => [
                'delimiter' => ","
            ]
        ]));
        $tableName = 'utl_task';
        $config = [
            [
                'attribute' => 'id',
                'value' => function ($line) {
                    return $line[0];
                },
                'unique' => true,
            ],
            [
                'attribute' => 'descrizione',
                'value' => function ($line) {
                    return iconv('CP1252', 'UTF8', $line[1]);
                },
                'unique' => true,
            ]
        ];
        $importer->import(new MultipleImportStrategy([
            'tableName' => $tableName,
            'configs' => $config,
        ]));
    }

    /**
     * Inserisci indirizzi scaricati da openaddresses e filtrati
     *
     * ./yii installer/parse-addresses
     * @return void
     */
    public function actionParseAddresses()
    {

        $n = 0;
        $n_c = 0;
        if (($handle = fopen(Yii::$app->params["ADDRESSES_FILE_NAME"], "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                $comune = $data[5];
                $c = LocComune::find()->where('LOWER(loc_comune.comune) = $$' . strtolower($comune) . '$$')->one();
                if (!$c) echo "Comune non trovato " . ucwords(strtolower($comune)) . "\n";

                $indirizzo = LocIndirizzo::find()->where(['name' => $data[3]])->andWhere(['id_comune' => $c->id])->one();
                if (!$indirizzo) :
                    $indirizzo = new LocIndirizzo();
                    $indirizzo->name = $data[3];
                    $indirizzo->id_comune = $c->id;
                    $indirizzo->comune_string = $c->comune;
                    if (!$indirizzo->save()) echo "Errore inserimento indirizzo " . $data[3] . "\n";
                endif;

                $civico = LocCivico::find()
                    ->where(['id_indirizzo' => $indirizzo->id])
                    ->andWhere(['civico' => $data[2]])
                    ->one();

                if (!$civico) $civico = new LocCivico();
                $civico->id_indirizzo = $indirizzo->id;
                $civico->civico = $data[2];
                $civico->lat = $data[0];
                $civico->lon = $data[1];
                $civico->cap = "" . $data[8];

                if (!$civico->save()) echo "Errore " . $indirizzo->name . " " . $data[2] . " " . $c->comune . " inserito \n";

                $n++;
            }
        }
    }

    /**
     * Inserisce lat e lon delle sedi senza coordinate prendendole da google
     *
     * ./yii installer/clean-addresses
     * @return void
     */
    public function actionCleanAddresses()
    {
        $sedi = VolSede::find()->where(['lat' => 0.0])->joinWith(['locComune', 'locComune.provincia'])->all();
        foreach ($sedi as $sede) {
            $address = $sede->indirizzo . " " .
                $sede->locComune->comune . " (" .
                $sede->locComune->provincia->sigla . ")";
            // prendo coordinate da google e le metto nella sede
            echo $address . "\n";

            $lat_lng = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . Yii::$app->params['google_key']);
            $res = json_decode($lat_lng, true);

            if (
                isset($res['results']) &&
                isset($res['results'][0]) &&
                isset($res['results'][0]['geometry']) &&
                isset($res['results'][0]['geometry']['location']) &&
                isset($res['results'][0]['geometry']['location']['lng'])
            ) :
                $sede->lat = $res['results'][0]['geometry']['location']['lat'];
                $sede->lon = $res['results'][0]['geometry']['location']['lng'];
                $sede->save();
            endif;
        }
    }
}
