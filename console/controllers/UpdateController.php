<?php 
namespace console\controllers;

use Yii;
use yii\console\Controller;

use common\models\VolOrganizzazione;
use common\models\VolTipoOrganizzazione;
use common\models\UtlAnagrafica;
use common\models\LocComune;
use common\models\VolSede;
use common\models\VolVolontario;
use common\models\UtlAutomezzo;
use common\models\UtlAutomezzoTipo;
use common\models\UtlAttrezzatura;
use common\models\UtlAttrezzaturaTipo;
use common\models\UtlSegnalazione;
use common\models\UtlEvento;

use yii\db\Migration;

class UpdateController extends Controller {
	
    

	/**
	 * Importa dati da File di export ZeroGis
	 *
	 * Identificativi fogli da caricare:
	 * 1 -> file organizzazioni che inizia con "Nr. Iscrizione"
	 * 2 -> file organizzazioni che inizia con "Nr. Regionale"
	 * 3 -> file mezzi e attrezzature
	 *
	 *
	 * ./yii update/import 1 "~/path/to/file.xls"
	 * @deprecated
	 * @param  [integer] $nfoglio   Identificativo foglio
	 * @param  [string] $file_path Path del file
	 * @return 
	 */
	public function actionImport($nfoglio, $file_path)
	{
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file_path);
		$reader->setReadDataOnly(true); 

		$worksheet = $reader->load($file_path);
		$rows = $worksheet->getActiveSheet()->toArray(null, false, true, false);
        

		switch($nfoglio) {
			case 1:
				$this->addOrganizzazioni($rows);
			break;
			case 2:
				$this->addOtherOrganizzazioniInfo($rows);
			break;
			case 3:
				$this->addProductionMezziAttrezzature($rows);
			break;
		}
	}


	/**
	 * Aggiungi/modifica organizzazioni in file che inizia con "Nr. Iscrizione"
	 * @param [type] $rows [description]
	 */
	private function addOrganizzazioni($rows)
    {
        $added = [];
        $n = 0;
        foreach ($rows as $row) {
            if($n > 0 && $row[2] != ''){
                $org = VolOrganizzazione::find()
                ->where(['denominazione'=>$row[3]])
                ->orWhere(['ref_id'=>$row[0]])
                ->one();
                if(!$org) :
                    $org = new VolOrganizzazione();
                    $org->ref_id = $row[0];
                    $org->denominazione = $row[3];
                    $org->note = "";//'codicert - '.$row[1];
                    $tipoOrg = VolTipoOrganizzazione::find()->where(['tipologia'=>$row[2]])->one();
                    if(!$tipoOrg) :
                        $tipoOrg = new VolTipoOrganizzazione();
                        $tipoOrg->tipologia = $row[2];
                        $tipoOrg->save();
                    endif;
                    $org->id_tipo_organizzazione = $tipoOrg->id;
                endif;
                $org->ref_id = intval($row[0]);
                $org->save();


                // cerca le sedi
                if($row[4] && $row[4] != "") :
                    $sede = VolSede::find()
                        ->where(['indirizzo'=>$row[4]])
                        ->andWhere(['id_organizzazione'=>$org->id])
                        ->one();
                    if(!$sede) :
                        $sede = new VolSede();
                    endif;
                    $sede->tipo = 'Sede Legale';
                    $sede->id_organizzazione = $org->id;
                    $sede->indirizzo = $row[4];
                    $sede->cap = $row[6];
                    if($row[5] && $row[5] != "") :
                        $comune = LocComune::find()
                        ->where(['comune' => $row[5]])
                        ->andWhere(['id_regione'=>Yii::$app->params['region_filter_id']])
                        ->one();
                        //echo $comune;
                        if($comune) : 
                            $sede->comune = $comune->id; 
                            else:
                            echo "Comune non trovato ".$row[5]."\n";
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
                if($org->id) $added[] = $org->ref_id;
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
                        ->where(['id_organizzazione'=>$del_org->id])
                        ->all();

            
            if($del_org->delete()) :
                echo "Cancellata organizzazione ".$del_org->denominazione."\n";
                foreach ($sede as $s) {
                    if($s->delete()) :
                        echo "Cancellata sede ".$s->indirizzo."\n";
                    endif;
                }
            else:
                echo "errore eliminazione organizzazione\n";
            endif;
        endforeach;
        

    }

    /**
     * Aggiungi organizzazioni aggiornando i dati del file che inizia con "Nr. Regionale"
     * @param [type] $rows [description]
     */
    private function addOtherOrganizzazioniInfo($rows)
    {
        
        $n = 0;
        $added = [];
        foreach ($rows as $row) {
            if($n > 0 && $row[1] != ''){
                $org = VolOrganizzazione::find()->where(['denominazione'=>$row[1]])->one();
                if($org) :

                    $org->codicefiscale = $row[8];
                    $org->partita_iva = $row[8];

                    $dt = \DateTime::createFromFormat('d/m/Y', $row[9]);
                    if($dt):
                        $org->data_costituzione = $dt->format('Y-m-d');
                    endif;
                    $org->num_albo_regionale = $row[11];
                    $dt = \DateTime::createFromFormat('d/m/Y', $row[12]);
                    if($dt):
                        $org->data_albo_regionale = $dt->format('Y-m-d');
                    endif;
                    $org->save();

                    $anagrafica = UtlAnagrafica::find()
                    ->where(['codfiscale'=>$row[22]])
                    ->one();
                    if(!$anagrafica) :
                        $anagrafica = new UtlAnagrafica();
                    endif;
                    $anagrafica->nome = $row[13];
                    $anagrafica->cognome = $row[14];
                    $dt = \DateTime::createFromFormat('d/m/Y', $row[16]);
                    if($dt):
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
                                    ->where(['id_organizzazione'=>$org->id])
                                    ->andWhere(['id_anagrafica'=>$anagrafica->id])
                                    ->one();

                    if(!$volontario) :
                        $volontario = new VolVolontario();
                    endif;

                    $volontario->ruolo = 'Rappresentante Legale';
                    $volontario->id_organizzazione = $org->id;
                    $volontario->id_anagrafica = $anagrafica->id;
                    $volontario->save();

                    if($org->id) $added[] = $org->ref_id;

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
                    ->where(['id_organizzazione'=>$del_org->id])
                    ->all();

            $volontario = $volontario = VolVolontario::find()
                    ->where(['id_organizzazione'=>$del_org->id])
                    ->all();

            if($del_org->delete()) :
                echo "Cancellata organizzazione ".$del_org->denominazione."\n";

                foreach ($sede as $s) {
                    if($s->delete()) :
                        echo "Cancellata sede ".$s->indirizzo."\n";
                    endif;
                }
                
                foreach ($volontario as $v) {
                    if($v->delete()) :
                        echo "Cancellato volontario\n";
                    endif;
                }

            else:
                echo "errore eliminazione organizzazione\n";
            endif;


        endforeach;
    }

    /**
     * Aggiungi mezzi e attrezzature da foglio ZeroGIS
     * @param [type] $rows [description]
     */
    private function addProductionMezziAttrezzature($rows)
    {
        
        $mezzi_refs = [];
        $attrz_refs = [];

        $n = 0;
        $id_sedi = [];
        //echo "Inizio loop file\n";
        foreach ($rows as $row) {
            if($n > 0) :
                //echo "Inserisco riga\n";
                $org_name = str_replace("Possesso: ", "", $row[5]);
                $org = VolOrganizzazione::find()->where(['denominazione'=>$org_name])->one();

                if($org) :                    
                    $sede = VolSede::find()
                        ->where(['id_organizzazione'=>$org->id])
                        ->one();
                    if($sede) :
                        // solo se la trova
                        switch($row[2]){
                            case 'ATTREZZATURE':
                                //echo 'attrezzatura';
                                $add = $this->addAttrezzatura($sede, $row, null, null);
                                if($add) : $attrz_refs[] = $add; endif;
                                //echo "ref_id, attrezzatura: ".$add."\n";
                            break;
                            case 'MEZZI':
                                //echo 'mezzo';
                                $add = $this->addAutomezzo($sede, $row, null, null);
                                if($add) : $mezzi_refs[] = $add; endif;
                                //echo "ref_id, automezzo: ".$add."\n";
                            break;
                            default:
                            echo "Tipologia non riconosciuta\n";
                            break;
                        }
                    endif;
                endif;
                echo $n."\n";
            endif;
            $n++;
        }
        
        

        $to_del_au = UtlAutomezzo::find()
        ->where(['not in', 'ref_id', $mezzi_refs])
        ->andWhere(['!=', 'ref_id', 0])
        ->andWhere('ref_id is not null')
        ->all();
        foreach ($to_del_au as $auto) {
            if($auto->delete()) echo "Automezzo eliminato\n";
        }

        $to_del_at = UtlAttrezzatura::find()
        ->where(['not in', 'ref_id', $attrz_refs])
        ->andWhere(['!=', 'ref_id', 0])
        ->andWhere('ref_id is not null')
        ->all();
        foreach ($to_del_at as $attrz) {
            if($attrz->delete()) echo "Attrezzatura eliminata\n";
        }
    }


    /**
     * gli indici sopra al 6 sono stati aumentati di 1 per produzione
     * @param [type] $sede    [description]
     * @param [type] $row     [description]
     * @param [type] $mzaereo [description]
     * @param [type] $mzaib   [description]
     */
    private function addAutomezzo($sede, $row, $mzaereo, $mzaib)
    {
        //echo "creo automezzo\n";
        $identificativo_risorsa = intval($row[0]);
        $automezzo = UtlAutomezzo::find()->where(['ref_id'=>$identificativo_risorsa])->one();
        if(!$automezzo) $automezzo = new UtlAutomezzo();

        //if($row[3] == 'ELICOTTERO' || $row[3] == 'MEZZO AEREO') $automezzo->is_mezzo_aereo = true;
        //if(strtolower( $row[19] ) == 's') $automezzo->idcategoria = $mzaib->id;
        
        $automezzo->ref_id = $identificativo_risorsa;

        $automezzo->targa = "".$row[15];
        $automezzo->modello = "".$row[33];
        $automezzo->allestimento = "".$row[67];

        $tipo = UtlAutomezzoTipo::find()->where(['descrizione'=>$row[3]])->one();
        if(!$tipo) :
            $tipo = new UtlAutomezzoTipo();
            $tipo->descrizione = $row[3];            
            if($row[3] == 'ELICOTTERO' || $row[3] == 'MEZZO AEREO') $tipo->is_mezzo_aereo = true;
            $tipo->save();
        endif;        

        $automezzo->idtipo = $tipo->id;
        $automezzo->idsede = $sede->id;
        $automezzo->idorganizzazione = $sede->id_organizzazione;
        
        if(!$automezzo->save()) {
            var_dump($automezzo->getErrors());
            return false;
        }

        return $automezzo->ref_id;
    }


    /**
     * gli indici sopra al 6 sono stati aumentati di 1 per produzione
     * @param [type] $sede    [description]
     * @param [type] $row     [description]
     * @param [type] $mzaereo [description]
     * @param [type] $mzaib   [description]
     */
    private function addAttrezzatura($sede, $row, $mzaereo, $mzaib)
    {

        //echo "creo attrezzatura\n";
        $identificativo_risorsa = intval($row[0]);
        $attrezzatura = UtlAttrezzatura::find()->where(['ref_id'=>$identificativo_risorsa])->one();
        if(!$attrezzatura) $attrezzatura = new UtlAttrezzatura();
        //if(strtolower( $row[19] ) == 's') $attrezzatura->idcategoria = $mzaib->id;
        $attrezzatura->ref_id = $identificativo_risorsa;
        $attrezzatura->modello = "".$row[33];
        $attrezzatura->allestimento = "".$row[67];

        $tipo = UtlAttrezzaturaTipo::find()->where(['descrizione'=>$row[3]])->one();
        if(!$tipo) :
            $tipo = new UtlAttrezzaturaTipo();
            $tipo->descrizione = $row[3];
            $tipo->save();
        endif;
        $attrezzatura->idtipo = $tipo->id;
        $attrezzatura->idsede = $sede->id;
        $attrezzatura->idorganizzazione = $sede->id_organizzazione;
        
        if(!$attrezzatura->save()) {
            var_dump($attrezzatura->getErrors());
            return false;
        }

        return $attrezzatura->ref_id;
    }

    /**
     * Aggiorna dati delle viste create per l'autocomplete di indirizzi
     * 
     * ./yii update/refresh-autocomplete-views
     * @return [type] [description]
     */
    public function actionRefreshAutocompleteViews()
    {
        echo "Ricarico le viste\n";
        Yii::$app->db->createCommand("REFRESH MATERIALIZED VIEW materialized_view_autocomplete_addresses;")->execute();
        Yii::$app->db->createCommand("REFRESH MATERIALIZED VIEW materialized_view_autocomplete_cap;")->execute();
        Yii::$app->db->createCommand("REFRESH MATERIALIZED VIEW materialized_view_autocomplete_comuni;")->execute();
        echo "Fine\n";
        return;
    }

    /**
     * Inserisci la geometria delle zone di allerta aggregandola dai comuni di appartenenza
     * ./yii update/add-geometry-zone-allerta
     * @return void
     */
    public function actionAddGeometryZoneAllerta()
    {
        Yii::$app->db->createCommand("
            WITH geometries as (SELECT 
                a.\"code\",
                ST_Union(c.geom) as zona_geometry
            FROM alm_zona_allerta a
            LEFT JOIN con_zona_allerta_comune cz ON cz.id_alm_zona_allerta = a.id 
            LEFT JOIN loc_comune_geom c ON cz.codistat_comune::int = c.pro_com_t::int
            GROUP BY a.id
            ORDER BY a.code ASC)
            UPDATE alm_zona_allerta au 
                        SET geom = (SELECT zona_geometry FROM geometries WHERE \"code\" = au.code);
            ")->execute();
    }
 
    /**
     * Inserisci i dati dei segnalatori piatti su utl_segnalazione
     * Mettere memoria php a -1
     *
     * ./yii update/change-segnalatori 1
     * @param  integer $commit 1 per committare
     * @return void
     */
    public function actionChangeSegnalatori( $commit = 0 ) 
    {

        $conn = Yii::$app->db;
        $dbTrans = $conn->beginTransaction();

        $segnalazioni = UtlSegnalazione::find()->all();
        $n = 0;
        foreach ($segnalazioni as $segnalazione) {
            $n++;
            echo "Segnalazione: $segnalazione->id\n";
            $segnalazione->nome_segnalatore = @$segnalazione->utente->anagrafica->nome;
            $segnalazione->cognome_segnalatore = @$segnalazione->utente->anagrafica->cognome;
            $segnalazione->email_segnalatore = @$segnalazione->utente->anagrafica->email;
            $segnalazione->telefono_segnalatore = (@$segnalazione->utente->anagrafica->telefono) ? $segnalazione->utente->anagrafica->telefono : @$segnalazione->utente->telefono;

            $segnalazione->save();
        }

        if($commit = 1) {
            $dbTrans->commit();
        } else {
            $dbTrans->rollBack();
        }
    }

    /**
     * Metti i datori di lavoro sugli ingaggi
     * ./yii update/set-datori-lavoro-ingaggio 1
     * @param  integer $commit [description]
     * @return [type]          [description]
     */
    public function actionSetDatoriLavoroIngaggio( $commit = 0 ) {
        
        $conn = Yii::$app->db;
        $dbTrans = $conn->beginTransaction();

        $conn = \common\models\ConVolontarioIngaggio::find()->all();
        foreach ($conn as $vol_ing) {
            if( !empty($vol_ing->volontario->datore_di_lavoro) ) {
                $vol_ing->datore_di_lavoro = $vol_ing->volontario->datore_di_lavoro;
                $vol_ing->save();
            }
        }

        if($commit = 1) {
            $dbTrans->commit();
        } else {
            $dbTrans->rollBack();
        }

    }

    public function actionChangeExtra(){
        $extra = \common\models\UtlExtraSegnalazione::find()->where(['voce'=>'Centro Operartivo Comunale'])->one();
        if($extra) {
            $extra->voce = 'Croce Rossa Italiana';
            $extra->save();
            echo "OK\n";
        }
    }

    /**
     * Aggiorna messaggi CAP mettendo expire
     * 
     * @return [type] [description]
     */
    public function actionAddCapExpires(){
        Yii::$app->db->createCommand(
            "UPDATE cap_messages SET expires = (json_content->'info'->>'expires')::TIMESTAMP"
        )->execute();
    }

    protected $migrate = null;
    private function normalize( $str ) {
        return preg_replace("/[^a-zA-Z0-9]/", "_", strtolower( $str ) );
    }

    public function actionImportRecupero($commit = 0) {
        
        $trans = Yii::$app->db->beginTransaction();

        $file = __DIR__ . '/../data/websor_recupero_dati/organizzazione.xlsx';

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile( $file );
        $reader->setReadDataOnly(true);

        $worksheet = $reader->load( $file );
        $worksheet->setActiveSheetIndex( 0 );

        $data = $worksheet->getActiveSheet()->toArray(null, false, true, false);
        $n_row = 0;

        $row_cols = [];
        $datas = [];
        $cols = [];

        $current_numero_regionale = null;
        $list_numeri_regionali_da_non_toccare = []; // qui ci mettiamo quelli ce non vanno messi non attivi
        
        $indicizzazione_specializzazioni = [];

        foreach ($data as $row) {
            // prima riga la escludiamo
            if($n_row == 0) {
                $n_row++;

                // specializzazioni 
                // da 54 - 70
                for($n = 54; $n <= 70; $n++) {
                    
                    $nome_specializzazione = $row[$n];
                    $spec = \common\models\TblSezioneSpecialistica::find()->where(['descrizione'=>$row[$n]])->one();

                    if(!$spec) {
                        echo "SPECIALIZZAZIONE NON PRESENTE: " . $row[$n] . "\n";
                        $spec = new \common\models\TblSezioneSpecialistica();
                        $spec->descrizione = $row[$n];
                        $spec->save();
                    }

                    $indicizzazione_specializzazioni[$n] = $spec;

                }

                continue;
            }     

            if(!empty($row[0])) {
                
                $current_numero_regionale = $row[0];
                $list_numeri_regionali_da_non_toccare[] = $row[0];

                $odv = \common\models\VolOrganizzazione::find()->where(['ref_id'=>$current_numero_regionale])->one();
                if(!$odv) {

                    $odv = new \common\models\VolOrganizzazione();
                    $odv->ref_id = $current_numero_regionale;

                    echo "ODV NON PRESENTE: " . $row[5] . "\n";
                    
                }


                $odv->stato_iscrizione = $row[4] == 'Attiva' ? 3 : 4;
                $odv->denominazione = $row[5];
                $odv->codicefiscale = preg_replace("/[^0-9]/", "", $row[7]);
                $odv->partita_iva = (!empty($row[6])) ? preg_replace("/[^0-9]/", "", $row[6]) : preg_replace("/[^0-9]/", "", $row[7]);

                // rappr. legale 9 - 10
                $odv->nome_responsabile = $row[9] . " " . $row[10];
                $odv->cf_rappresentante_legale = $row[11];
                $odv->num_albo_regionale = $row[30];
                if(!empty($row[31])){
                    $dt = \DateTime::createFromFormat("d/m/Y", $row[31]);
                    if(!is_bool($dt)) $odv->data_albo_regionale = $dt->format('Y-m-d');
                }

                $odv->save();

                

                Yii::$app->db->createCommand("DELETE FROM con_organizzazione_sezione_specialistica WHERE id_organizzazione = :id_odv", [ ':id_odv'=>$odv->id ])->execute();
                
                // verifica specializzazioni
                for($n = 54; $n <= 70; $n++) {

                    if(trim($row[$n]) == 'x') {
                        
                        $connessione = new \common\models\ConOrganizzazioneSezioneSpecialistica;
                        $connessione->id_sezione_specialistica = $indicizzazione_specializzazioni[$n]->id;
                        $connessione->id_organizzazione = $odv->id;
                        $connessione->save();

                    }
                }

                $id_recapiti_da_non_toccate = [];

                // recapiti messaggistica ODV
                // email -> $row[12] 0
                // pec -> $row[13] 1
                // tel -> $row[14] 2
                // fax -> $row[15] 3
                // telh24 -> $row[16] 4
                // faxh24 -> $row[17] 5
                if(!empty($row[12])) {
                    $recapiti = explode(";", $row[12]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_MESSAGGISTICA, 0);
                        }
                    }       
                }

                if(!empty($row[13])) {
                    $recapiti = explode(";", $row[13]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_MESSAGGISTICA, 1);
                        }
                    }       
                }

                if(!empty($row[14])) {
                    $recapiti = explode(";", $row[14]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_MESSAGGISTICA, 2);
                        }
                    }       
                }

                if(!empty($row[15])) {
                    $recapiti = explode(";", $row[15]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_MESSAGGISTICA, 3);
                        }
                    }       
                }

                if(!empty($row[16])) {
                    $recapiti = explode(";", $row[16]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_MESSAGGISTICA, 4);
                        }
                    }       
                }

                if(!empty($row[17])) {
                    $recapiti = explode(";", $row[17]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_MESSAGGISTICA, 5);
                        }
                    }       
                }




                // recapiti attivazioni
                // email -> $row[18]
                // pec -> $row[19]
                // tel -> $row[20]
                // fax -> $row[21]
                // telh24 -> $row[22]
                // faxh24 -> $row[23]
                if(!empty($row[18])) {
                    $recapiti = explode(";", $row[18]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_INGAGGIO, 0);
                        }
                    }       
                }

                if(!empty($row[19])) {
                    $recapiti = explode(";", $row[19]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_INGAGGIO, 1);
                        }
                    }       
                }

                if(!empty($row[20])) {
                    $recapiti = explode(";", $row[20]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_INGAGGIO, 2);
                        }
                    }       
                }

                if(!empty($row[21])) {
                    $recapiti = explode(";", $row[21]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_INGAGGIO, 3);
                        }
                    }       
                }

                if(!empty($row[22])) {
                    $recapiti = explode(";", $row[22]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_INGAGGIO, 4);
                        }
                    }       
                }

                if(!empty($row[23])) {
                    $recapiti = explode(";", $row[23]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_INGAGGIO, 5);
                        }
                    }       
                }


                // recapiti allertamenti
                // email -> $row[24]
                // pec -> $row[25]
                // tel -> $row[26]
                // fax -> $row[27]
                // telh24 -> $row[28]
                // faxh24 -> $row[29]
                if(!empty($row[24])) {
                    $recapiti = explode(";", $row[24]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_ALLERTA, 0);
                        }
                    }       
                }

                if(!empty($row[25])) {
                    $recapiti = explode(";", $row[25]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_ALLERTA, 1);
                        }
                    }       
                }

                if(!empty($row[26])) {
                    $recapiti = explode(";", $row[26]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_ALLERTA, 2);
                        }
                    }       
                }

                if(!empty($row[26])) {
                    $recapiti = explode(";", $row[26]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_ALLERTA, 3);
                        }
                    }       
                }

                if(!empty($row[27])) {
                    $recapiti = explode(";", $row[27]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_ALLERTA, 4);
                        }
                    }       
                }

                if(!empty($row[28])) {
                    $recapiti = explode(";", $row[28]);
                    if(count($recapiti) > 0) {
                        foreach ($recapiti as $recapito) {
                            $id_recapiti_da_non_toccate[] = $this->saveContattoOdv($odv, $recapito, \common\models\organizzazione\ConOrganizzazioneContatto::TIPO_ALLERTA, 5);
                        }
                    }       
                }

                $to_delete = \common\models\organizzazione\ConOrganizzazioneContatto::find()->where([
                    'id_organizzazione'=>$odv->id
                ])->andWhere(['not in', 'id', $id_recapiti_da_non_toccate])
                ->all();
                foreach ($to_delete as $con) {
                    $con->delete();
                }


                $this->addSede($odv, $data, $row[71], $row[72], $row[73], $row[74], $row[75], $row[76], $row[77], $row[78] );


            } else {
                // sto lavorando su una sede, i dati dell'odv sono stati già messi
                $this->addSede($odv, $data, $row[71], $row[72], $row[73], $row[74], $row[75], $row[76], $row[77], $row[78] );
            }


            $n_row++;
        }

        $orgs = \common\models\VolOrganizzazione::find()->where(['not in','ref_id',$list_numeri_regionali_da_non_toccare])->all();
        foreach ($orgs as $org) {
            $org->stato_iscrizione = 6;
            if(!$org->save()) throw new \Exception(json_encode($org->getErrors()), 1);
            
        }

        if($commit == 1) {
            $trans->commit();
        } else {
            $trans->rollBack();
        }
    }

    // ritorna id connessione
    private function saveContattoOdv($odv, $recapito, $use_type, $type) {
        $utl_contatto = \common\models\utility\UtlContatto::find()->where(['contatto'=>$recapito])->one();
        if(!$utl_contatto) {
            $utl_contatto = new \common\models\utility\UtlContatto;
            $utl_contatto->contatto = $recapito;
            $utl_contatto->type = $type;
            $utl_contatto->save();
        }

        $exist = \common\models\organizzazione\ConOrganizzazioneContatto::find()
        ->where(['id_organizzazione'=>$odv->id])
        ->andWhere(['id_contatto'=>$utl_contatto->id])
        ->andWhere(['use_type'=>$use_type])
        ->one();
        
        if(!$exist) {
            $exist = new \common\models\organizzazione\ConOrganizzazioneContatto();
            $exist->id_organizzazione = $odv->id;
            $exist->id_contatto = $utl_contatto->id;
            $exist->type = $type;
            $exist->use_type = $use_type;
            if(!$exist->save()) throw new \Exception(json_encode($exist->getErrors()), 1);
        } 

        return $exist->id;
        
    }

    private function mapTipo($t) {
        return (strtoupper($t) == 'SEDE LEGALE') ? 'Sede Legale' : 'Sede Operativa';
    }

    private function addSede($odv, $all_data, $nome, $tipo, $provincia, $comune, $indirizzo, $cap, $lon, $lat) {

        $_comune = \common\models\LocComune::find()->where([
            'comune' => $comune
        ])->one();

        if(!$_comune) throw new \Exception("Comune non trovato " . $comune, 1);
        
        if($tipo == 'Sede legale') {
            
            $sede_exist = \common\models\VolSede::find()
                ->where(['id_organizzazione'=>$odv->id])
                ->andWhere(['tipo'=>$this->mapTipo($tipo)])
                ->one();

        } else {

            $num_sedi = $this->getNumSediOperativeOdv($odv->ref_id, $all_data);
            if($num_sedi == 1) {
                $sede_exist = \common\models\VolSede::find()
                    ->where(['id_organizzazione'=>$odv->id])
                    ->andWhere(['tipo'=>$this->mapTipo($tipo)])
                    ->one();
            } else {
                $sede_exist = \common\models\VolSede::find()
                    ->where(['id_organizzazione'=>$odv->id])
                    ->andWhere(['comune'=>$_comune->id])
                    ->andWhere(['tipo'=>$this->mapTipo($tipo)])
                    ->andWhere(['cap'=>$cap])
                    ->all();
                if(count($sede_exist) == 1) {
                    $sede_exist = $sede_exist[0];
                } else {
                    $sede_exist = null;
                }
            }   

        }


        if(!$sede_exist) {
            $sede_exist = new \common\models\VolSede;
            $sede_exist->id_organizzazione = $odv->id;

            echo "NON ESISTE SEDE " . $this->mapTipo($tipo) . " -> " . $nome . " -> " . $odv->denominazione . "\n";
        } 

        $sede_exist->tipo = $this->mapTipo($tipo);
        $sede_exist->name = $nome;
        $sede_exist->cap = $cap;
        $sede_exist->indirizzo = $indirizzo;
        $sede_exist->comune = $_comune->id;
        $sede_exist->lat = $lat;
        $sede_exist->lon = $lon;
        if(!$sede_exist->save()) throw new \Exception(json_encode($sede_exist->getErrors()), 1);
        

    }

    private function getNumSediOperativeOdv($num_regionale, $data) {
        $n = 0;
        $found = false;
        $current_num = null;
        foreach ($data as $row) {

            if($row[0] == $num_regionale) $current_num = $row[0];

            if($current_num == $num_regionale) {
                if($row[72] == 'Sede operativa') $n++;
            }

        }
        return $n;
    }



    /**
     * Carica tabelle temporanee per i comuni
     * @param string $file_comuni_path path del file sql con i comuni
     * @param string $file_geom_comuni_path path del file con le geometrie
     * @return [type] [description]
     */
    public function actionImportComuniSqlFiles($file_comuni_path, $file_geom_comuni_path) {
        

        $trans = Yii::$app->db->beginTransaction();

        try {
            // carico comuni piatti
            $import = file_get_contents($file_comuni_path, true);
            $import = explode(";", $import);
            foreach ($import as $query) {            
                if(trim($query) != "") {
                    Yii::$app->db->createCommand($query)->execute();
                }
            }

            // carico comuni geometrici
            $import = file_get_contents($file_geom_comuni_path, true);
            $import = explode(";", $import);
            foreach ($import as $query) {            
                if(trim($query) != "") {
                    Yii::$app->db->createCommand($query)->execute();
                }
            }

            $trans->commit();

        } catch(\Exception $e) {

            $trans->rollBack();
            throw $e;
            
        }



    }

    /**
     * In base alla cartella temporanea aggiunge i comuni non presenti nella regione di riferimento
     * @return [type] [description]
     */
    public function actionAddComuniNonPresenti(){
        $trans = Yii::$app->db->beginTransaction();
        try {

            Yii::$app->db->createCommand("SELECT setval('loc_comune_id_seq', (SELECT MAX(id) FROM loc_comune));")->execute();
            Yii::$app->db->createCommand('INSERT INTO loc_comune(
                "id_regione", "id_provincia", "comune", "idstat", "zona_geografica", "codnuts2", "codnuts3", "codmetropoli",
                "codistat",
                "codcatasto",
                "provincia_sigla",
                "cap",
                "codregione",
                "isprovincia",
                "altitudine",
                "islitoraneo",
                "codmontano",
                "superficie",
                "popolazione2011",
                "prefisso_tel",
                "zona_altimetrica"
                ) SELECT 
                id_regione, id_provincia, comune, null, zona_geografica, codnuts2, codnuts3, codmetropoli,
                codistat,
                codcatasto,
                provincia_sigla,
                cap,
                codregione,
                isprovincia,
                altitudine,
                islitoraneo,
                codmontano,
                superficie,
                popolazione2011,
                prefisso_tel,
                zona_altimetrica
                FROM tmp_loc_comune_2021
                WHERE (SELECT count(*) FROM loc_comune WHERE loc_comune.codistat = tmp_loc_comune_2021.codistat::TEXT) = 0;')
            ->execute();

            Yii::$app->db->createCommand("ALTER TABLE loc_comune_geom RENAME TO old_loc_comune_geom;")->execute();
            Yii::$app->db->createCommand("ALTER TABLE tmp_loc_comune_geom RENAME TO loc_comune_geom;")->execute();

            Yii::$app->db->createCommand("UPDATE loc_comune SET soppresso = true WHERE id in (SELECT id FROM loc_comune 
                LEFT JOIN loc_comune_geom ON loc_comune_geom.pro_com::INTEGER = loc_comune.codistat::INTEGER
                WHERE loc_comune_geom.gid is null);")->execute();


            $trans->commit();

        } catch(\Exception $e) {
            $trans->rollBack();
            throw $e;
        }
    }

    /**
     * Chiusura evento retrodatata
     * @param  [type] $id_evento [description]
     * @param  [type] $dataora   Y-m-d H:i:s
     * @return [type]            [description]
     */
    public function actionCloseEventoWithDate($id_evento, $dataora) {

        $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $dataora);
        if(is_bool($dt)) throw new \Exception("Data non valida", 1);

        $trans = Yii::$app->db->beginTransaction();
        try {
        
            $evento = UtlEvento::findOne($id_evento);
            if(!$evento) throw new \Exception("Evento non trovato", 1);
            
            if($evento->stato == 'Chiuso') throw new \Exception("Evento già chiuso", 1);
            
            $evento->stato = 'Chiuso';
            $evento->closed_at = $dataora;
            $evento->save();

            echo "Chiuso";

            $trans->commit();
        } catch(\Exception $e) {
            $trans->rollBack();
            throw $e;
        }
    }

}