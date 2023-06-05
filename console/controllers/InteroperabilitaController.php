<?php

namespace console\controllers;

use common\models\app\config\Keys;
use common\models\cap\CapMessages;
use common\models\cap\CapResources;
use common\models\LocComune;
use common\models\organizzazione\ConOrganizzazioneContatto;
use common\models\organizzazione\ConSedeContatto;
use common\models\TblSezioneSpecialistica;
use common\models\utility\UtlContatto;
use common\models\utility\UtlIndirizzo;
use common\models\UtlAnagrafica;
use common\models\UtlAttrezzatura;
use common\models\UtlAttrezzaturaTipo;
use common\models\UtlAutomezzo;
use common\models\UtlAutomezzoTipo;
use common\models\UtlSpecializzazione;
use common\models\VolVolontario;
use common\models\VolOrganizzazione;
use common\models\VolSede;
use common\models\VolTipoOrganizzazione;
use common\models\tabelle\TblTipoRisorsaMeta;

//getTraceAsString
use common\models\app\AppSyncErrorLog;
use Exception;
use Yii;
use yii\console\Controller;

use common\utils\cap\CapFeed;
use common\utils\integrations\mgo\MgoHttpServices;
use yii\helpers\Console;
use common\models\app\AppConfig;

class InteroperabilitaController extends Controller
{
    public function printItem($i)
    {
        return print_r($i, true);
    }

    public $debug = 0;
    /**
     * Sync Mgo data
     *
     * ./Yii interoperabilita/sync-mgo
     *
     * @return void
     */
    public function actionSyncMgo($commit = 0, $debug = 0)
    {
        $conf = AppConfig::findOne(['key'=>'last_mgo_sync']);
        if (!$conf) {
            $conf = new AppConfig();
            $conf->label = 'SYNC MGO';
            $conf->key = 'last_mgo_sync';
        }

        $this->debug = $debug;

        $this->actionLogin($debug);
        $this->actionGetOdv($commit, $debug);
        $this->actionGetVolontario($commit, $debug);
        $this->actionGetRisorsa($commit, $debug);

        $json_value = json_encode([
            'date' => (new \DateTime())->format('d/m/Y H:i:s')
        ]);

        $conf->value = $json_value;
        if (!$conf->save()) {
            AppSyncErrorLog::createError('conf', "CONFIGURAZIONE NON SALVATA: " . $this->printItem($conf->getErrors()));
        }
        //\Yii::$app->runAction('action/method');
    }

    // /**
    //  * Action Login to MGO services
    //  * @return [type] [description]
    //  */
    public function actionLogin($debug = 0)
    {
        $res = MgoHttpServices::login();
        if ($debug == 1) {
            $this->stdout(var_dump($res), Console::BOLD);
        }
    }

    /**
     * Action add organizzazione/convenzione
     * @return [type] [description]
     */
    // public function actionAddConvenzione()
    // {
    //     $res = MgoHttpServices::addConvenzione(58091);
    //     $this->stdout(var_dump($res), Console::BOLD);
    // }

    /**
     * Action list odv
     * @return [type] [description]
     */
    public function actionGetOdv($commit = 0, $debug = 0)
    {
        $this->debug = $debug;
        // Prima chiamata al servizio per recuperare il pageCount
        $resForCount = MgoHttpServices::getOdv();
        $pageCount = 0;
        if (!empty($resForCount['data']['_meta']['pageCount'])) {
            $pageCount = $resForCount['data']['_meta']['pageCount'];
        }

        // Ciclo sul page count e passa la pagina corrente al servizio
        for ($i = 1; $i <= $pageCount; $i++) {
            $res = MgoHttpServices::getOdv($i);
            if (!empty($res['data']['list'])) {
                foreach ($res['data']['list'] as $key => $item) {
                    $tx = Yii::$app->db->beginTransaction();
                    try {
                        // Set data
                        $odv = [];

                        // ++++++ SAVE/UPDATE Tipologia Organizzazione +++++++
                        $tipoOrganizzazione = VolTipoOrganizzazione::findOne(['id_sync' => 'MGO_' . $item['id_tipo_organizzazione']]);
                        if (empty($tipoOrganizzazione)) {
                            $tipoOrganizzazione = new VolTipoOrganizzazione();
                        }

                        $odv['VolTipoOrganizzazione']['tipologia'] = !empty($item['tipo_organizzazione_descrizione']) ? $item['tipo_organizzazione_descrizione'] : null;
                        $odv['VolTipoOrganizzazione']['id_sync'] = !empty($item['id_tipo_organizzazione']) ? "MGO_" . $item['id_tipo_organizzazione'] : null;

                        // Save VolTipoOrganizzazione
                        if (!$tipoOrganizzazione->load($odv) || !$tipoOrganizzazione->save()) {
                            throw new \Exception("Tipologia organizzazione NOT SAVED\nERROR: " . $this->printItem($tipoOrganizzazione->getErrors()), 1);
                        }

                        // ++++++ SAVE/UPDATE Organizzazione +++++++
                        $organizzazione = VolOrganizzazione::find()->where(['id_sync' => 'MGO_' . $item['id_organizzazione']])->orWhere(['codicefiscale' => $item['cf']])->one();
                        if (empty($organizzazione)) {
                            $organizzazione = new VolOrganizzazione();
                        }

                        $odv['VolOrganizzazione']['id_tipo_organizzazione'] = !empty($tipoOrganizzazione) ? $tipoOrganizzazione->id : null;
                        $odv['VolOrganizzazione']['denominazione'] = !empty($item['denominazione']) ? $item['denominazione'] : null;
                        $odv['VolOrganizzazione']['id_sync'] = !empty($item['id_organizzazione']) ? "MGO_" . $item['id_organizzazione'] : null;
                        $odv['VolOrganizzazione']['ref_id'] = !empty($item['num_elenco_territoriale']) ? $item['num_elenco_territoriale'] : null;
                        $odv['VolOrganizzazione']['codicefiscale'] = !empty($item['cf']) ? $item['cf'] : null;
                        $odv['VolOrganizzazione']['piva'] = !empty($item['piva']) ? $item['piva'] : null;
                        $odv['VolOrganizzazione']['ambito'] = !empty($item['ambito']) ? $item['ambito'] : null;
                        $odv['VolOrganizzazione']['data_costituzione'] = !empty($item['data_costituzione']) ? $item['data_costituzione'] : null;
                        $odv['VolOrganizzazione']['rappresentante_legale'] = !empty($item['rappresentante_legale']) ? $item['rappresentante_legale'] : null;
                        $odv['VolOrganizzazione']['cf_rappresentante_legale'] = !empty($item['cf_rappresentante_legale']) ? $item['cf_rappresentante_legale'] : null;
                        $odv['VolOrganizzazione']['nome_referente'] = !empty($item['referente']) ? $item['referente'] : null;
                        $odv['VolOrganizzazione']['cf_referente'] = !empty($item['cf_referente']) ? $item['cf_referente'] : null;
                        $odv['VolOrganizzazione']['stato_iscrizione'] = !empty($item['stato_iscrizione']) ? $item['stato_iscrizione'] : null;

                        // Save VolOrganizzazione
                        if (!$organizzazione->load($odv) || !$organizzazione->save()) {
                            throw new \Exception("Organizzazione NOT SAVED\nERROR: " . $this->printItem($organizzazione->getErrors()), 1);
                        }

                        // ++++++ SAVE/UPDATE Sede Organizzazione +++++++
                        $sedeOrganizzazione = VolSede::findOne(['id_sync' => 'MGO_' . $item['id_sede']]);
                        if (empty($sedeOrganizzazione) && $item['tipo_sede'] == 0) {
                            $sedeOrganizzazione = VolSede::findOne(['id_organizzazione' => $organizzazione->id, 'tipo' => 'Sede Legale']);
                        }

                        if (empty($sedeOrganizzazione)) {
                            $sedeOrganizzazione = new VolSede();
                        }

                        $odv['VolSede']['id_organizzazione'] = !empty($organizzazione) ? $organizzazione->id : null;
                        $odv['VolSede']['id_sync'] = !empty($item['id_sede']) ? 'MGO_' . $item['id_sede'] : null;
                        $odv['VolSede']['name'] = !empty($item['nome_sede']) ? $item['nome_sede'] : null;
                        $odv['VolSede']['lat'] = !empty($item['lat']) ? $item['lat'] : null;
                        $odv['VolSede']['lon'] = !empty($item['lon']) ? $item['lon'] : null;
                        $odv['VolSede']['cap'] = !empty($item['cap_sede']) ? $item['cap_sede'] : null;
                        $odv['VolSede']['indirizzo'] = !empty($item['indirizzo_sede'] && trim($item['indirizzo_sede']) != '') ? $item['indirizzo_sede'] : "INDIRIZZO NON DEFINITO";
                        $odv['VolSede']['disponibilita_oraria'] = !empty($item['disp_h_text']) ? $item['disp_h_text'] : null;
                        $odv['VolSede']['tipo'] = ($item['tipo_sede'] == 0) ? 'Sede Legale' : 'Sede Operativa';

                        $locCoumuneSede = LocComune::findOne(['codistat' => $item['codistat_comune_sede']]);
                        $odv['VolSede']['comune'] = !empty($locCoumuneSede) ? $locCoumuneSede->id : null;

                        // Save VolSede
                        if (!$sedeOrganizzazione->load($odv) || !$sedeOrganizzazione->save()) {
                            throw new \Exception("Sede NOT SAVED\nERROR: " . $this->printItem($sedeOrganizzazione->getErrors()), 1);
                        }

                        // ++++++ SAVE/UPDATE Contatto Organizzazione +++++++
                        $this->upsertContattoOrganizzazione($organizzazione, $item);

                        // ++++++ SAVE/UPDATE Contatto Sede +++++++
                        $this->upsertContattoSede($sedeOrganizzazione, $item);

                        // ++++++ SAVE/UPDATE Specializzazioni +++++++
                        $this->upsertSpecializzazioni($organizzazione, $item);

                        if ($commit == 1) {
                            $tx->commit();
                        } else {
                            $tx->rollBack();
                        }
                    } catch (\Exception $e) {
                        $tx->rollBack();

                        if ($this->debug == 1) {
                            echo "ERRORE, NON SALVO ODV\n";
                            echo $this->printItem($item) . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n";
                        }
                        AppSyncErrorLog::createError('odv', $this->printItem($item) . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
                        Yii::error($this->printItem($item) . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString(), 'sync');
                    }
                }
            }
        }
    }

    /**
     * Action list volontario
     * @return [type] [description]
     */
    public function actionGetVolontario($commit = 0, $debug = 0)
    {
        $this->debug = $debug;
        // Prima chiamata al servizio per recuperare il pageCount
        $resForCount = MgoHttpServices::getVolontario();
        if (!empty($resForCount['data']['_meta']['pageCount'])) {
            $pageCount = $resForCount['data']['_meta']['pageCount'];
        }

        $_n = 0;
        // Ciclo sul page count e passa la pagina corrente al servizio
        for ($i = 1; $i <= $pageCount; $i++) {
            if ($debug == 1) {
                echo "RICHIAMO PAGINA " . $i . "\n";
            }
            $res = MgoHttpServices::getVolontario($i);
            if ($debug == 1) {
                echo "RICEVUTO RISULTATO PAGINA " . $i . "\n";
            }

            if (!empty($res['data']['list'])) {
                foreach ($res['data']['list'] as $item) {
                    $_n++;
                    if ($debug == 1) {
                        echo "\nSALVO VOLONTARIO N. " . $_n . " ID: " . $item['id'] . "\n";
                    }

                    $tx = Yii::$app->db->beginTransaction();
                    try {
                        // Set data
                        $odv = [];

                        // Check if exist Organizzazione if not exist skip insert volontario
                        $organizzazione = VolOrganizzazione::findOne(
                            ['codicefiscale' => $item['cf_organizzazione']]
                        );
                        if (empty($organizzazione)) {
                            throw new \Exception('SYNC VOLONTARIO - Organizzazione non trovata per questo CF: ' . $item['cf_organizzazione'], 1);
                        }

                        // Check if exist Sede Organizzazione
                        $sedeOrganizzazione = VolSede::findOne(['id_sync' => 'MGO_' . $item['id_sede']]);

                        if (empty($item['cf'])) {
                            throw new \Exception("Codice fiscale volontario non presente: ID: " . $item['id'], 1);
                        }
                        // ++++++ SAVE/UPDATE Anagrafica +++++++
                        $anagrafica = UtlAnagrafica::find()
                            ->where(['id_sync' => 'MGO_' . $item['id_anagrafica']])
                            ->one();

                        if (empty($anagrafica)) {
                            $anagrafica = UtlAnagrafica::find()
                            ->where(['codfiscale'=>$item['cf']])
                            ->one();

                            if (empty($anagrafica)) {
                                $anagrafica = new UtlAnagrafica();
                            }
                        }

                        $odv['UtlAnagrafica']['codfiscale'] = !empty($item['cf']) ? $item['cf'] : null;
                        $odv['UtlAnagrafica']['nome'] = !empty($item['nome']) ? $item['nome'] : null;
                        $odv['UtlAnagrafica']['cognome'] = !empty($item['cognome']) ? $item['cognome'] : null;
                        $odv['UtlAnagrafica']['data_nascita'] = !empty($item['data_nascita']) ? $item['data_nascita'] : null;
                        $odv['UtlAnagrafica']['luogo_nascita'] = !empty($item['luogo_nascita']) ? $item['luogo_nascita'] : null;

                        //$comuneResidenza = !empty($item['residenza']['comune']['codistat']) ? LocComune::findOne(['codistat' => $item['residenza']['comune']['codistat']]) : null;
                        //$odv['UtlAnagrafica']['comune'] = !empty($comuneResidenza) ? $comuneResidenza->id : null;
                        //$odv['UtlAnagrafica']['indirizzo_residenza'] = !empty($item['residenza']['full_address']) ? $item['residenza']['full_address'] : null;
                        //$odv['UtlAnagrafica']['cap_residenza'] = !empty($item['residenza']['cap']) ? $item['residenza']['cap'] : null;
                        $odv['UtlAnagrafica']['id_sync'] = !empty($item['id_anagrafica']) ? 'MGO_' . $item['id_anagrafica'] : null;

                        // Save Anagrafica
                        if (!$anagrafica->load($odv) || !$anagrafica->save()) {
                            throw new \Exception("SYNC VOLONTARIO - Anagrafica NOT SAVED CF: " . $item['cf'] . "\nERROR: " . $this->printItem($anagrafica->getErrors()), 1);
                        }

                        // ++++++ SAVE/UPDATE Volontario +++++++
                        $volontario = VolVolontario::find()
                            ->where(['id_sync' => 'MGO_' . $item['id']])->one();
                        if (empty($volontario)) {
                            $volontario = new VolVolontario();
                        }

                        $odv['VolVolontario']['id_sync'] = !empty($item['id']) ? 'MGO_' . $item['id'] : null;
                        $odv['VolVolontario']['id_anagrafica'] = !empty($anagrafica) ? $anagrafica->id : null;
                        $odv['VolVolontario']['id_organizzazione'] = !empty($organizzazione) ? $organizzazione->id : null;
                        $odv['VolVolontario']['id_sede'] = !empty($sedeOrganizzazione) ? $sedeOrganizzazione->id : null;
                        $odv['VolVolontario']['operativo'] = !empty($item['stato']) ? true : false;
                        // Save Volontario
                        if (!$volontario->load($odv) || !$volontario->save()) {
                            throw new \Exception("SYNC VOLONTARIO - Volontario NOT SAVED ID: " . $item['id'] . "\nERROR: " . $this->printItem($volontario->getErrors()), 1);
                        }

                        // ++++++ SAVE/UPDATE Volontario Indirizzo Residenza +++++++
                        //$indirizzoResidenza = UtlIndirizzo::find()->where(['id_sync' => 'MGO_' . $item['residenza']['id']])->one();
                        //if (empty($indirizzoResidenza)) {
                        //    $indirizzoResidenza = new UtlIndirizzo();
                        //}

                        //$indirizzoResidenza->id_comune = !empty($comuneResidenza) ? $comuneResidenza->id : null;
                        //$indirizzoResidenza->indirizzo = !empty($item['residenza']['indirizzo']) ? $item['residenza']['indirizzo'] : null;
                        //$indirizzoResidenza->civico = !empty($item['residenza']['civico']) ? $item['residenza']['civico'] : null;
                        //$indirizzoResidenza->cap = !empty($item['residenza']['cap']) ? $item['residenza']['cap'] : null;
                        //$indirizzoResidenza->id_sync = !empty($item['residenza']['id']) ? 'MGO_' . $item['residenza']['id'] : null;

                        // Save Indirizzo Residenza
                        /*
                        if ($indirizzoResidenza->save()) {
                            $linked_ana_r = $anagrafica->getIndirizzo()
                                        ->where(['id'=>$indirizzoResidenza->id])->one();

                            $linked_vol_r = $volontario->getIndirizzo()
                                ->where(['id'=>$indirizzoResidenza->id])->one();

                            // Link anagrafica/indirizzo e volontario/indirizzo
                            if(!$linked_ana_r) $anagrafica->link('indirizzo', $indirizzoResidenza);
                            if(!$linked_vol_r) $volontario->link('indirizzo', $indirizzoResidenza);
                            //$this->stdout("\nIndirizzo residenza SAVED", Console::FG_GREEN);
                        } else {
                            $this->stdout("\nSYNC VOLONTARIO - Indirizzo residenza NOT SAVED\nERROR: " . json_encode($indirizzoResidenza->getErrors()), Console::FG_RED);
                        }
                        */

                        // ++++++ SAVE/UPDATE Indirizzo +++++++
                        /*
                        if (!empty($item['indirizzo'])) {
                            foreach ($item['indirizzo'] as $key => $indirizzo) {

                                // Check if indirizzo is saved in db else new indirizzo
                                $indirizzoMd = UtlIndirizzo::find()->where(['id_sync' => 'MGO_' . $indirizzo['id']])->one();
                                if (empty($indirizzoMd)) {
                                    $indirizzoMd = new UtlIndirizzo();
                                }

                                $comuneIndirizzo = !empty($indirizzo['comune']['codistat']) ? LocComune::findOne(['codistat' => $indirizzo['comune']['codistat']]) : null;
                                $indirizzoMd->id_comune = !empty($comuneIndirizzo) ? $comuneIndirizzo->id : null;
                                $indirizzoMd->indirizzo = !empty($indirizzo['indirizzo']) ? $indirizzo['indirizzo'] : null;
                                $indirizzoMd->civico = !empty($indirizzo['civico']) ? $indirizzo['civico'] : null;
                                $indirizzoMd->cap = !empty($indirizzo['cap']) ? $indirizzo['cap'] : null;
                                $indirizzoMd->id_sync = !empty($indirizzo['id']) ? 'MGO_' . $indirizzo['id'] : null;

                                // Save Indirizzo
                                if ($indirizzoMd->save()) {

                                    $linked_ana_i = $anagrafica->getIndirizzo()
                                        ->where(['id'=>$indirizzoMd->id])->one();

                                    $linked_vol_i = $volontario->getIndirizzo()
                                        ->where(['id'=>$indirizzoMd->id])->one();

                                    // Link anagrafica/indirizzo e volontario/indirizzo
                                    if(!$linked_ana_i) $anagrafica->link('indirizzo', $indirizzoMd);
                                    if(!$linked_vol_i) $volontario->link('indirizzo', $indirizzoMd);
                                    //$this->stdout("\nIndirizzo SAVED", Console::FG_GREEN);
                                } else {
                                    $this->stdout("\nSYNC VOLONTARIO - Indirizzo NOT SAVED\nERROR: " . json_encode($indirizzoMd->getErrors()), Console::FG_RED);
                                    Yii::error($indirizzoMd->errors, 'sync');
                                }
                            }
                        }
                        */

                        // ++++++ SAVE/UPDATE Contatto +++++++
                        /*
                        if (!empty($item['contatto'])) {

                            foreach ($item['contatto'] as $key => $contatto) {

                                // Check if contatto is saved in db else new contatto
                                $contattoMd = UtlContatto::find()->where(['id_sync' => 'MGO_' . $contatto['id']])->one();
                                if (empty($contattoMd)) {
                                    $contattoMd = new UtlContatto();
                                }

                                $contattoMd->type = !empty($contatto['type']) ? $contatto['type'] : null;
                                $contattoMd->contatto = !empty($contatto['contatto']) ? $contatto['contatto'] : null;
                                $contattoMd->note = !empty($contatto['note']) ? $contatto['note'] : null;
                                $contattoMd->check_mobile = !empty($contatto['check_mobile']) ? $contatto['check_mobile'] : null;
                                $contattoMd->use_type = !empty($contatto['use_type']) ? $contatto['use_type'] : null;
                                $contattoMd->id_sync = !empty($contatto['id']) ? 'MGO_' . $contatto['id'] : null;

                                // Save Contatto
                                if ($contattoMd->save()) {

                                    $linked_ana = $anagrafica->getContatto()
                                        ->where(['id'=>$contattoMd->id])->one();

                                    $linked_vol = $volontario->getContatto()
                                        ->where(['id'=>$contattoMd->id])->one();

                                    // Link anagrafica/contatto e volontario/contatto
                                    if(!$linked_ana) $anagrafica->link('contatto', $contattoMd, ['use_type' => $contatto['use_type'], 'type' => $contatto['type']]);

                                    if(!$linked_vol) $volontario->link('contatto', $contattoMd, ['use_type' => $contatto['use_type'], 'type' => $contatto['type']]);

                                    //$this->stdout("\nContatto SAVED", Console::FG_GREEN);
                                } else {
                                    $this->stdout("\nSYNC VOLONTARIO - Contatto NOT SAVED\nERROR: " . json_encode($contattoMd->getErrors()), Console::FG_RED);
                                    Yii::error($contattoMd->errors, 'sync');
                                }
                            }
                        }
                        */

                        // ++++++ SAVE/UPDATE Specializzazione +++++++
                        if (isset($item['specializzazione']) && !empty($item['specializzazione'])) {
                            foreach ($item['specializzazione'] as $key => $specializzazione) {
                                // Check if specializzazione is saved in db else new specializzazione
                                $specializzazioneMd = UtlSpecializzazione::find()->where(['id_sync' => 'MGO_' . $specializzazione['id']])->one();
                                if (empty($specializzazioneMd)) {
                                    $specializzazioneMd = new UtlSpecializzazione();
                                }

                                $specializzazioneMd->descrizione = !empty($specializzazione['descrizione']) ? $specializzazione['descrizione'] : null;
                                $specializzazioneMd->id_sync = !empty($specializzazione['id']) ? 'MGO_' . $specializzazione['id'] : null;

                                // Save Specializzazione
                                if ($specializzazioneMd->save()) {
                                    $has = $volontario->getSpecializzazione()->where(['id'=>$specializzazioneMd->id])->one();
                                    // Link volontario/specializzazione
                                    if (!$has) {
                                        $volontario->link('specializzazione', $specializzazioneMd);
                                    }

                                    if ($this->debug == 1) {
                                        $this->stdout("\nSpecializzazione SAVED", Console::FG_GREEN);
                                    }
                                } else {
                                    if ($this->debug == 1) {
                                        $this->stdout("\nSYNC VOLONTARIO - Specializzazione NOT SAVED\nERROR: " . $this->printItem($specializzazioneMd->getErrors()), Console::FG_RED);
                                    }

                                    AppSyncErrorLog::createError('volontario', "SYNC VOLONTARIO - Specializzazione NOT SAVED\nERROR: " . $this->printItem($specializzazioneMd->getErrors()), 'WARNING');

                                    Yii::error($specializzazioneMd->errors, 'sync');
                                }
                            }
                        }

                        if ($commit == 1) {
                            $tx->commit();
                        } else {
                            $tx->rollBack();
                        }
                    } catch (\Exception $e) {
                        $tx->rollBack();

                        if ($this->debug == 1) {
                            echo "ERRORE, NON SALVO VOLONTARIO\n";
                            echo $this->printItem($item) . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n";
                        }

                        AppSyncErrorLog::createError('volontario', $this->printItem($item) . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
                        Yii::error($this->printItem($item) . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString(), 'sync');
                    }
                }
            }
        }
    }

    /**
     * Action list risorsa
     * @return [type] [description]
     */
    public function actionGetRisorsa($commit = 0, $debug = 0)
    {
        $this->debug = $debug;
        // Prima chiamata al servizio per recuperare il pageCount
        $resForCount = MgoHttpServices::getRisorsa();
        if (!empty($resForCount['data']['_meta']['pageCount'])) {
            $pageCount = $resForCount['data']['_meta']['pageCount'];
        }

        // Ciclo sul page count e passa la pagina corrente al servizio
        for ($i = 0; $i <= $pageCount; $i++) {
            $res = MgoHttpServices::getRisorsa($i);

            if (!empty($res['data']['list'])) {
                foreach ($res['data']['list'] as $key => $item) {
                    $tx = Yii::$app->db->beginTransaction();
                    try {
                        // Check if exist Organizzazione if not exist skip insert volontario
                        $organizzazione = VolOrganizzazione::findOne(['codicefiscale' => $item['cf']]);
                        if (empty($organizzazione)) {
                            throw new \Exception("SYNC RISORSA - Organizzazione non trovata, CF: " . $item['cf'], 1);
                        }

                        // Check if exist Sede Organizzazione
                        $sedeOrganizzazione = VolSede::findOne(['id_sync' => 'MGO_' . $item['id_sede']]);

                        // ++++++ SAVE/UPDATE Risorsa +++++++
                        if ($item['categoria_risorsa'] === 'MEZZI') {
                            // Check tipo risorsa
                            $risorsaTipo = UtlAutomezzoTipo::findOne(['id_sync' => 'MGO_' . $item['id_tipo_risorsa']]);
                            if (empty($risorsaTipo)) {
                                $risorsaTipo = new UtlAutomezzoTipo();
                            }

                            // Check risorsa
                            $risorsa = UtlAutomezzo::findOne(['id_sync' => 'MGO_' . $item['id']]);
                            if (empty($risorsa)) {
                                $risorsa = new UtlAutomezzo();
                            }
                        } else {
                            // Check tipo risorsa
                            $risorsaTipo = UtlAttrezzaturaTipo::findOne(['id_sync' => 'MGO_' . $item['id_tipo_risorsa']]);
                            if (empty($risorsaTipo)) {
                                $risorsaTipo = new UtlAttrezzaturaTipo();
                            }

                            // Check risorsa
                            $risorsa = UtlAttrezzatura::findOne(['id_sync' => 'MGO_' . $item['id']]);
                            if (empty($risorsa)) {
                                $risorsa = new UtlAttrezzatura();
                            }
                        }

                        // Load Tipo Risorsa
                        $risorsaTipo->descrizione = $item['tipo_risorsa'];
                        $risorsaTipo->id_sync = 'MGO_' . $item['id_tipo_risorsa'];

                        // Save Tipo Risorsa
                        if ($risorsaTipo->save()) {
                            //$this->stdout("\nTipo risorsa SAVED", Console::FG_GREEN);
                        } else {
                            throw new \Exception("SYNC RISORSA - Tipo risorsa non trovata: " . $item['tipo_risorsa'], 1);
                        }

                        // Load Risorsa
                        $risorsa->idorganizzazione = $organizzazione->id;
                        $risorsa->idsede = $sedeOrganizzazione->id;
                        $risorsa->idtipo = $risorsaTipo->id;
                        $risorsa->id_sync = 'MGO_' . $item['id'];
                        $risorsa->disponibilita = "" . $item['disponibilita'];
                        if (!empty($risorsa->unita)) {
                            $risorsa->unita = $item['quantita'];
                        }

                        // Get Metakeys and set array $metaKeysValues
                        $metaKeysValues = [];
                        try {
                            foreach ($item['risRisorsaMeta'] as $meta) {
                                $metaKeysValues[$meta['meta']['key']] = $meta['meta_value'];
                                try {
                                    $tipo_meta = TblTipoRisorsaMeta::findOne(['id_sync' => 'MGO_' . $meta['meta']['id']]);
                                    if (!$tipo_meta) {
                                        $tipo_meta = new TblTipoRisorsaMeta();
                                        $tipo_meta->id_sync = 'MGO_' . $meta['meta']['id'];
                                    }

                                    $tipo_meta->key = $meta['meta']['key'];
                                    $tipo_meta->extra = $meta['meta']['extra'];
                                    $tipo_meta->label = $meta['meta']['label'];
                                    $tipo_meta->type = $meta['meta']['type'];

                                    if (!$tipo_meta->save()) {
                                        if ($this->debug == 1) {
                                            $this->stdout("ERRORE SALVATAGGIO META\nERROR: ".$this->printItem($tipo_meta->getErrors()), Console::FG_RED);
                                        }

                                        AppSyncErrorLog::createError('risorsa', "ERRORE SALVATAGGIO META\nERROR: ".$this->printItem($tipo_meta->getErrors()), "WARNING");
                                    }
                                } catch (\Exception $e) {
                                    if ($this->debug == 1) {
                                        $this->stdout("ERRORE SALVATAGGIO META\nERROR: ".$e->getMessage() . "\n" . $e->getTraceAsString(), Console::FG_RED);
                                    }

                                    AppSyncErrorLog::createError('risorsa', $e->getMessage() . "\n" . $e->getTraceAsString(), "WARNING");
                                }
                            }
                        } catch (\Exception $e) {
                            if ($this->debug == 1) {
                                $this->stdout("\nErrore aggiornamento meta NOT SAVED\nERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString(), Console::FG_RED);
                            }

                            AppSyncErrorLog::createError('risorsa', 'SYNC RISORSA - Errore aggiornamento meta ' . $e->getMessage() . "\n" . $e->getTraceAsString(), "WARNING");

                            Yii::error($e->getMessage() . "\n" . $e->getTraceAsString(), 'sync');

                            //var_dump($e->getMessage());
                        }

                        // $this->stdout("\nMETA metaKeysValues", Console::FG_BLUE);
                        // var_dump($metaKeysValues);

                        // Get Meta Values
                        foreach ($metaKeysValues as $keyMeta => $value) {
                            switch ($keyMeta) {
                                case 'campo004':
                                    $m_field = 'targa';
                                    break;
                                case 'campo022':
                                    $m_field = 'modello';
                                    break;
                                case 'campo056':
                                    $m_field = 'allestimento';
                                    break;
                                case 'campo003':
                                    $m_field = 'tempo_attivazione';
                                    break;
                                case 'campo009':
                                case 'campo016':
                                    $m_field = 'capacita';  // AIB , SPARGISALE
                                    break;
                                default:
                                    $m_field = null;
                                    break;
                            }

                            if (!empty($m_field) && $risorsa->hasAttribute($m_field)) {
                                $risorsa->$m_field = $value;
                                //$this->stdout("\nMETA field {$m_field}", Console::FG_BLUE);
                            }
                        }

                        $risorsa->meta = $metaKeysValues;
                        // Save Risorsa
                        if (!$risorsa->save(false)) {
                            throw new \Exception("SYNC RISORSA - Risorsa NOT SAVED, ERROR: " .$this->printItem($risorsa->getErrors()), 1);
                        }

                        if ($commit == 1) {
                            $tx->commit();
                        } else {
                            $tx->rollBack();
                        }
                    } catch (\Exception $e) {
                        $tx->rollBack();

                        if ($this->debug == 1) {
                            echo "ERRORE, NON SALVO RISORSA\n";
                            echo $this->printItem($item) . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n";
                        }

                        AppSyncErrorLog::createError('risorsa', $this->printItem($item) . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
                        Yii::error($this->printItem($item) . "\n" . $e->getMessage() . "\n" . $e->getTraceAsString(), 'sync');
                    }
                }
            }
        }
    }

    /**
     * Action add contatto organizzazione
     * @return [type] [description]
     */
    private function upsertContattoOrganizzazione($organizzazione, $item)
    {
        // Array con gli id_sync della tabella di connessione con_organizzazione_contatto già salvati ['MGO_1'=>$modelConnessione]
        $conOrgContattoArray = ConOrganizzazioneContatto::find()
            ->where(['id_organizzazione' => $organizzazione->id])
            //->indexBy('id_sync')
            ->indexBy(function ($row) {
                return $row['id_sync'] . "|||" . $row['use_type'];
            })
            //->asArray()
            ->all();

        // Creo $arrayDeletedItems array di appoggio per gli item cancellati
        $arrayToNotDelete = [];

        // Foreach sui dati che arrivano dal servizio
        foreach ($item['contatti_odv'] as $key => $contatto) {
            $checkIndex = 'MGO_' . $contatto['id_connessione'] . "|||" . $contatto['use_type'];
            $contatto_index = 'MGO_' . $contatto['id_contatto'];
            // Al primo inserimento non avrò record salvati sul db
            if (empty($conOrgContattoArray)) {
                //Insert contatto
                $mdContatto =  new UtlContatto();
                $contatto['id_sync'] = 'MGO_' . $contatto_index;
                if ($mdContatto->load(['UtlContatto' => $contatto]) && $mdContatto->save()) {
                    $organizzazione->link('contatto', $mdContatto, ['use_type' => $contatto['use_type'], 'id_sync' => 'MGO_' . $contatto['id_connessione']]);
                    if ($this->debug == 1) {
                        $this->stdout("\nContatto SAVED", Console::FG_GREEN);
                    }
                } else {
                    if ($this->debug == 1) {
                        $this->stdout("\nContatto NOT SAVED " . json_encode($mdContatto->getErrors()), Console::FG_RED);
                    }
                    AppSyncErrorLog::createError(
                        'odv',
                        "Contatto NOT SAVED\n".$this->printItem($item) . "\n" . $this->printItem($mdContatto->getErrors()),
                        "WARNING"
                    );
                    Yii::error($mdContatto->errors, 'sync');
                }
            } else {
                // Se non esiste id_sync lo inserisco altrimenti aggiorno i dati di contatto

                if (empty($conOrgContattoArray[$checkIndex])) {
                    $mdContatto = UtlContatto::findOne(['id_sync'=>$contatto_index]);
                    if (!$mdContatto) {
                        $mdContatto = new UtlContatto();
                    }

                    $mdContatto->load(['UtlContatto' => $contatto]);
                    $mdContatto->id_sync = $contatto_index;

                    //Save contatto e add link
                    if ($mdContatto->save()) {
                        $organizzazione->link('contatto', $mdContatto, ['use_type' => $contatto['use_type'], 'id_sync' => 'MGO_' . $contatto['id_connessione']]);
                        if ($this->debug == 1) {
                            $this->stdout("\nUpsert Contatto SAVED", Console::FG_GREEN);
                        }

                        $arrayToNotDelete[$checkIndex] = true;
                    } else {
                        if ($this->debug == 1) {
                            $this->stdout("\nUpsert Contatto NOT SAVED " . json_encode($mdContatto->getErrors()), Console::FG_RED);
                        }
                        AppSyncErrorLog::createError(
                            'odv',
                            "Upsert Contatto NOT SAVED\n".$this->printItem($item) . "\n" . $this->printItem($mdContatto->getErrors()),
                            "WARNING"
                        );
                        Yii::error($mdContatto->errors, 'sync');
                    }
                } else {
                    //Update contatto
                    $mdContatto = UtlContatto::findOne(['id' => $conOrgContattoArray[$checkIndex]->id_contatto]);
                    if ($mdContatto->load(['UtlContatto' => $contatto]) && $mdContatto->save()) {
                        if ($this->debug == 1) {
                            $this->stdout("\nUpsert Contatto SAVED", Console::FG_GREEN);
                        }
                    } else {
                        if ($this->debug == 1) {
                            $this->stdout("\nUpsert Contatto NOT SAVED " . json_encode($mdContatto->getErrors()), Console::FG_RED);
                        }
                        AppSyncErrorLog::createError(
                            'odv',
                            "Upsert Contatto NOT SAVED\n".$this->printItem($item) . "\n" . $this->printItem($mdContatto->getErrors()),
                            "WARNING"
                        );
                        Yii::error($mdContatto->errors, 'sync');
                    }

                    // popolo $arrayToNotDelete per verifica contatti cancellati
                    $arrayToNotDelete[$checkIndex] = true;
                }
            }
        }

        // Ciclo i dati presenti nel db per verificare se ci sono record cancellati
        foreach ($conOrgContattoArray as $key => $insertedContatto) {
            if (/*!empty($arrayToNotDelete) && */!isset($arrayToNotDelete[$key])) {
                // Rimuovo la relazione
                $organizzazione->unlink('conContatto', $insertedContatto);
            }
        }
    }

    /**
     * Action add contatto sede
     * @return [type] [description]
     */
    private function upsertContattoSede($sede, $item)
    {
        // Array con gli id_sync della tabella di connessione con_sede_contatto già salvati ['MGO_1'=>$modelConnessione]
        $conSedeContattoArray = ConSedeContatto::find()
            ->where(['id_sede' => $sede->id])
            //->indexBy('id_sync')
            //->asArray()
            ->indexBy(function ($row) {
                return $row['id_sync'] . "|||" . $row['use_type'];
            })
            ->all();

        // Creo $arrayDeletedItems array di appoggio per gli item cancellati
        $arrayToNotDelete = [];

        // Foreach sui dati che arrivano dal servizio
        foreach ($item['contatti_sede'] as $key => $contatto) {
            $checkIndex = 'MGO_' . $contatto['id_connessione'] . "|||" . $contatto['use_type'];
            $contatto_index = 'MGO_' . $contatto['id_contatto'];

            // Al primo inserimento non avrò record salvati sul db
            if (empty($conSedeContattoArray)) {
                //Insert contatto
                $mdContatto =  new UtlContatto();
                $contatto['id_sync'] = $contatto_index;
                if ($mdContatto->load(['UtlContatto' => $contatto]) && $mdContatto->save()) {
                    $sede->link('contatto', $mdContatto, ['use_type' => $contatto['use_type'], 'id_sync' => 'MGO_' . $contatto['id_connessione']]);
                    if ($this->debug == 1) {
                        $this->stdout("\nContatto sede SAVED", Console::FG_GREEN);
                    }
                } else {
                    if ($this->debug == 1) {
                        $this->stdout("\nContatto sede NOT SAVED " . json_encode($mdContatto->getErrors()), Console::FG_RED);
                    }
                    AppSyncErrorLog::createError(
                        'odv',
                        "Contatto sede NOT SAVED\n".$this->printItem($item) . "\n" . $this->printItem($mdContatto->getErrors()),
                        "WARNING"
                    );
                    Yii::error($mdContatto->errors, 'sync');
                }
            } else {
                // Se non esiste id_sync lo inserisco altrimenti aggiorno i dati di contatto

                if (empty($conSedeContattoArray[$checkIndex])) {
                    $mdContatto = UtlContatto::findOne(['id_sync'=>$contatto_index]);
                    if (!$mdContatto) {
                        $mdContatto = new UtlContatto();
                    }

                    $mdContatto->load(['UtlContatto' => $contatto]);
                    $mdContatto->id_sync = $contatto_index;

                    //Save contatto e add link
                    if ($mdContatto->save()) {
                        $sede->link('contatto', $mdContatto, ['use_type' => $contatto['use_type'], 'id_sync' => 'MGO_' . $contatto['id_connessione']]);
                        if ($this->debug == 1) {
                            $this->stdout("\nUpsert Contatto Sede SAVED", Console::FG_GREEN);
                        }
                        $arrayToNotDelete[$checkIndex] = true;
                    } else {
                        if ($this->debug == 1) {
                            $this->stdout("\nUpsert Contatto Sede NOT SAVED " . json_encode($mdContatto->getErrors()), Console::FG_RED);
                        }
                        AppSyncErrorLog::createError(
                            'odv',
                            "Upsert Contatto Sede NOT SAVED\n".$this->printItem($item) . "\n" . $this->printItem($mdContatto->getErrors()),
                            "WARNING"
                        );
                        Yii::error($mdContatto->errors, 'sync');
                    }
                } else {
                    //Update contatto
                    $mdContatto = UtlContatto::findOne(['id' => $conSedeContattoArray[$checkIndex]->id_contatto]);
                    if ($mdContatto->load(['UtlContatto' => $contatto]) && $mdContatto->save()) {
                        if ($this->debug == 1) {
                            $this->stdout("\nUpsert Contatto SAVED", Console::FG_GREEN);
                        }
                    } else {
                        if ($this->debug == 1) {
                            $this->stdout("\nUpsert Contatto NOT SAVED " . json_encode($mdContatto->getErrors()), Console::FG_RED);
                        }
                        AppSyncErrorLog::createError(
                            'odv',
                            "Upsert Contatto Sede NOT SAVED\n".$this->printItem($item) . "\n" . $this->printItem($mdContatto->getErrors()),
                            "WARNING"
                        );
                        Yii::error($mdContatto->errors, 'sync');
                    }

                    // popolo $arrayCheckDeletedItems per verifica contatti cancellati
                    $arrayToNotDelete[$checkIndex] = true;
                }
            }
        }

        // Ciclo i dati presenti nel db per verificare se ci sono record cancellati
        foreach ($conSedeContattoArray as $key => $insertedContatto) {
            if (/*!empty($arrayCheckDeletedItems) && */!isset($arrayToNotDelete[$key])) {
                // Rimuovo la relazione
                $sede->unlink('conContatto', $insertedContatto);
            }
        }
    }

    /**
     * Action add specializzazione
     * @return [type] [description]
     */
    private function upsertSpecializzazioni($organizzazione, $item)
    {
        // Loop specailizzazioni
        if (!empty($item['specializzazioni'])) {
            // Delete old relations to organizzazione
            $organizzazione->unlinkAll('sezioneSpecialistica');

            foreach ($item['specializzazioni'] as $key => $spec) {
                // Check if exist record in TblSezioneSpecialistica
                $sezioneSpecialistica = TblSezioneSpecialistica::findOne(['id_sync' => 'MGO_' . $spec['id_sezione_specialistica']]);
                if (empty($sezioneSpecialistica)) {
                    $sezioneSpecialistica = new TblSezioneSpecialistica();
                }

                // Save Sezione Specialistica
                $sezioneSpecialistica->id_sync = !empty($spec['id_sezione_specialistica']) ? 'MGO_' . $spec['id_sezione_specialistica'] : null;
                $sezioneSpecialistica->descrizione = !empty($spec['descrizione_sezione_specialistica']) ? $spec['descrizione_sezione_specialistica'] : null;
                if ($sezioneSpecialistica->save()) {
                    // Link sezione specialistica to organizzazione
                    $organizzazione->link('sezioneSpecialistica', $sezioneSpecialistica);
                    if ($this->debug == 1) {
                        $this->stdout("\nSezione Specialistica SAVED", Console::FG_GREEN);
                    }
                } else {
                    if ($this->debug == 1) {
                        $this->stdout("\nSezione Specialistica NOT SAVED", Console::FG_RED);
                    }
                    AppSyncErrorLog::createError(
                        'odv',
                        "Upsert Contatto Sede NOT SAVED\n".$this->printItem($item) . "\n" . $this->printItem($sezioneSpecialistica->getErrors()),
                        "WARNING"
                    );
                    Yii::error($sezioneSpecialistica->errors, 'sync');
                }
            }
        }
    }
}
