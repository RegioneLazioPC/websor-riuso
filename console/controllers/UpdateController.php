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


}