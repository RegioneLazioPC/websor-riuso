<?php

namespace common\components;

use Yii;
use proj4php\Proj4php;
use proj4php\Proj;
use proj4php\Point;

class Syncer
{
    public $message;
    private $transaction;

    public static function replaceDispH($obj)
    {
        try {
            switch ($obj['organizzazione']['volOrganizzazione'][0]['disp_h']) {
                case 0:
                    return "H6";
                    break;
                case 1:
                    return "H12";
                    break;
                case 2:
                    return "H24";
                    break;
            }
        } catch (\Exception $e) {
            return "";
        }
    }

    private $contact_type_map = [
        "Email" => 0,
        "Pec" => 1,
        "Telefono" => 2,
        "Fax" => 3,
        "Tel h24" => 4,
        "Fax h24" => 5,
        "Sito web" => 7,
    ];

    /**
     * Su MGO sono aggregate
     * @var [type]
     */
    private $map_risorsa_type = [
        'MEZZI' => 'automezzo',
        'ATTREZZATURE' => 'attrezzatura'
    ];

    public function __construct($message)
    {
        $this->message = $message;
        $conn = \Yii::$app->db;
        $this->transaction = $conn->beginTransaction();
    }

    /**
     * Effettua le query
     */
    public function __destruct()
    {
        $this->transaction->commit();
    }

    /**
     * Ritorna una serie di contatti con implode("-")
     * Viene usata per i contatti dell'organizzazione che da excel quando multipli erano joinati con -
     * @param  array  $array [description]
     * @param  string $type  [description]
     * @return [type]        [description]
     */
    private function getContatti($array = [], $type = 'Email', $use_type = 0)
    {
        try {
            $arr = [];
            foreach ($array as $contatto) {
                if ($contatto['type'] == $type && $contatto['use_type'] == $use_type) {
                    $arr[] = $contatto['contatto'];
                }
            }

            $c = array_unique($arr);

            return implode("-", $c);
        } catch (\Exception $e) {
            return "";
        }
    }

    /**
     * Helper per organizzazione
     * @param  [type] $id [description]
     * @param  [type] $cf [description]
     * @return [type]     [description]
     */
    private function getOrganizzazione($id, $cf, $ref_id = null)
    {
        $org = \common\models\VolOrganizzazione::find()
        ->where(['id_sync'=>"MGO_".$id])
        ->one();

        if (!$org && !empty($cf)) {
            $org = \common\models\VolOrganizzazione::find()
            ->where(['codicefiscale'=>$cf])
            ->one();
        }

        if (!$org && !empty($ref_id)) {
            $org = \common\models\VolOrganizzazione::find()
            ->where(['ref_id'=>$ref_id])
            ->one();
        }

        if (!$org) {
            Yii::error('Organizzazione non trovata');
        }


        return ($org) ? $org : false;
    }

    /**
     * Helper per ente
     * @param  [type] $id [description]
     * @param  [type] $cf [description]
     * @return [type]     [description]
     */
    private function getEnte($id, $cf, $ref_id = null)
    {
        $org = \common\models\ente\EntEnte::find();
        $org->where(['id_sync'=>"MGO_".$id]);//->orWhere(['codicefiscale'=>$cf]);

        //if(!empty($ref_id)) $org->orWhere(['ref_id'=>$ref_id]);

        return $org->one();
    }

    /**
     * Helper per organizzazione
     * @param  [type] $id [description]
     * @param  [type] $cf [description]
     * @return [type]     [description]
     */
    private function getStruttura($id, $cf, $ref_id = null)
    {
        $org = \common\models\struttura\StrStruttura::find();
        $org->where(['id_sync'=>"MGO_".$id]);//->orWhere(['codicefiscale'=>$cf]);

        //if(!empty($ref_id)) $org->orWhere(['ref_id'=>$ref_id]);

        return $org->one();
    }

    private function getVolSedeTipo($tipo)
    {
        switch ($tipo) {
            case 0:
                return 'Sede Legale';
                break;
            default:
                return 'Sede Operativa';
                break;
        }
    }

    /**
     * Helper per sede
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    private function getSede($sede_array, $id_organizzazione)
    {
        $sede = \common\models\VolSede::find()->where(['id_sync'=>"MGO_".$sede_array['id']])->one();
        if (!$sede) {
            // ho l'organizzazione
            $sedi_org = \common\models\VolSede::find()->where(['id_organizzazione'=>$id_organizzazione])
            ->andWhere(['tipo'=>$this->getVolSedeTipo($sede_array['type'])])
            ->andWhere('id_sync is null')
            ->all();
            /**
             * Se per questa tipologia l'org ha più di una sede o nessuna va creata
             */
            if (count($sedi_org) > 1 || count($sedi_org) == 0) {
                return false;
            }
            // se ne ha solo una uso quella
            return $sedi_org[0];
        } else {
            return $sede;
        }
    }


    private function getEnteStrutturaSedeTipo($tipo)
    {
        return $tipo;
    }

    /**
     * Helper per sede
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    private function getEnteSede($sede_array, $id_ente)
    {
        $sede = \common\models\ente\EntEnteSede::find()->where(['id_sync'=>"MGO_".$sede_array['id']])->one();
        if (!$sede) {
            // ho l'organizzazione
            $sedi_org = \common\models\ente\EntEnteSede::find()->where(['id_ente'=>$id_ente])
            ->andWhere(['tipo'=>$this->getEnteStrutturaSedeTipo($sede_array['type'])])
            ->andWhere('id_sync is null')
            ->all();
            /**
             * Se per questa tipologia l'org ha più di una sede o nessuna va creata
             */
            if (count($sedi_org) > 1 || count($sedi_org) == 0) {
                return false;
            }
            // se ne ha solo una uso quella
            return $sedi_org[0];
        } else {
            return $sede;
        }
    }

    /**
     * Helper per sede
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    private function getStrutturaSede($sede_array, $id_struttura)
    {
        $sede = \common\models\struttura\StrStrutturaSede::find()->where(['id_sync'=>"MGO_".$sede_array['id']])->one();
        if (!$sede) {
            // ho l'organizzazione
            $sedi_org = \common\models\struttura\StrStrutturaSede::find()->where(['id_struttura'=>$id_struttura])
            ->andWhere(['tipo'=>$this->getEnteStrutturaSedeTipo($sede_array['type'])])
            ->andWhere('id_sync is null')
            ->all();
            /**
             * Se per questa tipologia l'org ha più di una sede o nessuna va creata
             */
            if (count($sedi_org) > 1 || count($sedi_org) == 0) {
                return false;
            }
            // se ne ha solo una uso quella
            return $sedi_org[0];
        } else {
            return $sede;
        }
    }

    /**
     * Helper per risorsa
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    private function getRisorsa($id, $ref_id = null)
    {
        if (!empty($ref_id)) {
            $ref_id = intval($ref_id);
        }

        $ris = \common\models\UtlAutomezzo::find()->where(['id_sync'=>"MGO_".$id])->one();
        if (!empty($ref_id) && empty($ris)) {
            $ris = \common\models\UtlAutomezzo::find()->where(['ref_id'=>intval($ref_id)])->one();
        }

        if (!$ris) {
            $ris = \common\models\UtlAttrezzatura::find()->where(['id_sync'=>"MGO_".$id]);
            if (!empty($ref_id)) {
                $ris->orWhere(['ref_id'=>intval($ref_id)]);
            }
            $ris = $ris->one();
        }

        return $ris;
    }

    /**
     * Helper per tipo risorsa
     * ritorna un nuovo
     * @param  [type] $id [description]
     * @return [object]     UtlAutomezzoTipo || UtlAttrezzaturaTipo || null
     */
    private function getTipoRisorsa($id, $desc, $type = 'automezzo')
    {
        switch ($type) {
            case 'automezzo':
                $t = \common\models\UtlAutomezzoTipo::find()->where(['id_sync'=>"MGO_".$id])->orWhere(['descrizione'=>$desc])->one();
                break;
            case 'attrezzatura':
                $t = \common\models\UtlAttrezzaturaTipo::find()->where(['id_sync'=>"MGO_".$id])->orWhere(['descrizione'=>$desc])->one();
                break;
        }

        return $t;
    }

    /**
     * Helper per tipo risorsa meta
     * ritorna un nuovo
     * @param  [type] $id [description]
     * @return [object]     UtlAutomezzoTipo || UtlAttrezzaturaTipo || null
     */
    private function getTipoRisorsaMeta($id, $key)
    {
        $t = \common\models\tabelle\TblTipoRisorsaMeta::find()->where(['id_sync'=>"MGO_".$id])
                ->orWhere(['key'=>$key])
                ->one();

        return $t;
    }

    /**
     * Helper per tipo organizzazione
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    private function getTipoOrganizzazione($id, $desc)
    {
        return \common\models\VolTipoOrganizzazione::find()->where(['id_sync'=>"MGO_".$id])->orWhere(['tipologia'=>$desc])->one();
    }

    /**
     * Helper per tipo organizzazione
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    private function getTipoEnte($id, $desc)
    {
        return \common\models\ente\EntTipoEnte::find()->where(['id_sync'=>"MGO_".$id])->orWhere(['descrizione'=>$desc])->one();
    }

    /**
     * Helper per tipo organizzazione
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    private function getTipoStruttura($id, $desc)
    {
        return \common\models\struttura\StrTipoStruttura::find()->where(['id_sync'=>"MGO_".$id])->orWhere(['descrizione'=>$desc])->one();
    }

    /**
     * Helper per specializzazione volontario
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    private function getSpecializzazione($id, $desc)
    {
        return \common\models\UtlSpecializzazione::find()->where(['id_sync'=>"MGO_".$id])->orWhere(['descrizione'=>$desc])->one();
    }

    /**
     * Helper per sezione specialistica organizzazione
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    private function getSezioneSpecialistica($id, $desc)
    {
        return \common\models\TblSezioneSpecialistica::find()->where(['id_sync'=>"MGO_".$id])->orWhere(['descrizione'=>$desc])->one();
    }

    /**
     * Helper per sezione specialistica organizzazione
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    private function getRuoloVolontario($id, $desc)
    {
        return \common\models\TblRuoloVolontario::find()->where([
            'id_sync'=>"MGO_".$id
        ])->orWhere([
            'UPPER(descrizione)'=>strtoupper($desc)
        ])->one();
    }

    /**
     * Torna il valore di un campo meta della risorsa
     * @param  [type] $data [description]
     * @param  [type] $meta [description]
     * @return [type]       [description]
     */
    private function getMetaRisorsaValue($data, $meta)
    {
        /*
        targa
        classe
        sottoclasse
        modello
        data_immatricolazione
        allestimento
        capacita
         */
        $m_field = '';
        switch ($meta) {
            case 'targa':
                $m_field = 'campo004';
                break;
            case 'classe':
                return false;
                break;
            case 'sottoclasse':
                return false;
                break;
            case 'modello':
                $m_field = 'campo022';
                break;
            case 'data_immatricolazione':
                return false;
                break;
            case 'allestimento':
                $m_field = 'campo056';
                break;
            case 'tempo_attivazione':
                $m_field = 'campo003';
                break;
            case 'capacita':
                $m_field = ['campo009','campo016'];// AIB , SPARGISALE
                break;
        }


        foreach ($data['risRisorsaMeta'] as $meta_val) {
            if (!empty($meta_val['meta']) &&
                // se è un array
                // come capacità ad esempio
                // prendiamo se è presente
                // difficilmente uno spargisale è anche un aib
                (is_array($m_field)) ? in_array($meta_val['meta']['key'], $m_field) : $meta_val['meta']['key'] == $m_field
            ) {
                return $meta_val['meta_value'];
            }
        }
        return false;
    }

    /**
     * Sincronizza i contatti di un model
     * :
     * Sede
     * Organizzazione
     * Volontario
     * Anagrafica
     *
     * Cancella quelli che dovrebbero essere stati eliminati
     * Aggiorna quelli presenti
     * Aggiunge quelli mancanti
     * @param  [type] $model [description]
     * @param  [type] $array [description]
     * @return [type]        [description]
     */
    private function syncContatti($model, $array)
    {
        /**
         * Per ogni contatto del model attuale
         * verifichiamo che ci sia anche in quello nuovo
         * se non presente lo rimuoviamo
         */

        foreach ($model->conContatto as $contatto) {
            $to_remove = true;
            // $n mi tiene traccia degli indici

            foreach ($array as $k => $cont) {
                if (!empty($contatto->id_sync) && $contatto->id_sync == "MGO_".$cont['id']) {
                    $to_remove = false;
                    // il contatto corrisponde,
                    // lo aggiorniamo
                    $contatto->contatto->load($this->objectMapping($cont, 'contatto'));
                    if (!$contatto->contatto->save()) {
                        Yii::error($contatto->contatto->getErrors());

                        Yii::error('non valido: ' . $contatto->contatto->contatto);
                    //throw new \Exception("Errore salvataggio ".json_encode($contatto->contatto->getErrors()));
                    } else {
                        if (!empty($cont['note'])) {
                            $contatto->note = $cont['note'];
                        }
                        if (!empty($cont['type'])) {
                            $contatto->type = $cont['type'];
                        }
                        if (!$contatto->save()) {
                            Yii::error('Errore salvataggio connessione contatto ' . json_encode($contatto->getErrors()));
                        }
                    }
                    // rimuovo il contatto trovato dall'array,
                    // così rimarranno solo quelli da inserire

                    unset($array[$k]);
                }
            }
            if ($to_remove) {
                $contatto->delete();
            }
        }

        foreach ($array as $contatto) {
            $c = new \common\models\utility\UtlContatto();
            $c->load($this->objectMapping($contatto, 'contatto'));
            if (!$c->save()) {
                Yii::error($c->getErrors());
                Yii::error('non valido: ' . $c->contatto);
            //throw new \Exception("Errore salvataggio ".json_encode($c->getErrors()));
            } else {
                $model->link('contatto', $c, [
                    'note' => @$contatto['note'],
                    'use_type' => $contatto['use_type'],
                    'type' => $contatto['type'],
                    'id_sync' => 'MGO_'.$contatto['id']
                ]);
            }
        }
    }

    /**
     * Sincronizzazione indirizzi per anagrafica e volontario
     * @param  [type] $model [description]
     * @param  [type] $array [description]
     * @return [type]        [description]
     */
    private function syncIndirizzi($model, $array)
    {
        /**
         * Per ogni contatto del model attuale
         * verifichiamo che ci sia anche in quello nuovo
         * se non presente lo rimuoviamo
         */

        foreach ($model->indirizzo as $indirizzo) {
            $to_remove = true;
            // $n mi tiene traccia degli indici

            foreach ($array as $k => $ind) {
                if (!empty($indirizzo->id_sync) && $indirizzo->id_sync == "MGO_".$ind['id']) {
                    $to_remove = false;
                    // il indirizzo corrisponde,
                    // lo aggiorniamo
                    $indirizzo->load($this->objectMapping($ind, 'indirizzo'));
                    if (!$indirizzo->save()) {
                        Yii::error($indirizzo->getErrors());
                        throw new \Exception("Errore salvataggio ".json_encode($indirizzo->getErrors()));
                    }
                    // rimuovo il indirizzo trovato dall'array,
                    // così rimarranno solo quelli da inserire

                    unset($array[$k]);
                }
            }
            if ($to_remove) {
                $indirizzo->delete();
            }
        }

        foreach ($array as $indirizzo) {
            $c = new \common\models\utility\UtlIndirizzo();
            $c->load($this->objectMapping($indirizzo, 'indirizzo'));
            if (!$c->save()) {
                Yii::error($c->getErrors());
                throw new \Exception("Errore salvataggio ".json_encode($c->getErrors()));
            }
            $model->link('indirizzo', $c);
        }
    }

    /**
     * Prendi organizzazioni con referente o rappresentante legale l'anagrafica e aggiornane i riferimenti
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    private function syncOrgDataWithAna($data)
    {
        $org_ref = \common\models\VolOrganizzazione::find()->where(['cf_referente'=>$data['cf']])->one();
        if ($org_ref) {
            $org_ref->nome_referente = @$data['nome'] . " " . @$data['cognome'];
            if (!$org_ref->save()) {
                Yii::error($org_ref->getErrors());
                throw new \Exception("Errore salvataggio ".json_encode($org_ref->getErrors()));
            }
        }

        $org_rapp = \common\models\VolOrganizzazione::find()->where(['cf_rappresentante_legale'=>$data['cf']])->one();
        if ($org_rapp) {
            $org_rapp->nome_referente = @$data['nome'] . " " . @$data['cognome'];
            if (!$org_rapp->save()) {
                Yii::error($org_rapp->getErrors());
                throw new \Exception("Errore salvataggio ".json_encode($org_rapp->getErrors()));
            }
        }
    }


    /**
     * La separo per poter gestire le singole info
     * verranno aggiornate da organizzazione volontario e anagrafica
     * meglio centralizzare e astrarre
     * @return [type] [description]
     */
    private function syncAnagrafica($data)
    {
        if (preg_match("/^RAND_/", $data['cf'])) {
            return false;
        }

        try {
            if (empty($data['cf'])) {
                $ana = \common\models\UtlAnagrafica::find()->where(['id_sync'=>"MGO_".$data['id']])->andWhere('codfiscale is null')->one();
            } else {
                $ana = \common\models\UtlAnagrafica::find()->where(['codfiscale'=>$data['cf']])->one();
            }

            if ($ana) {
                $ana->id_sync = "MGO_".$data['id'];
            }

            if (!$ana) {
                $ana = new \common\models\UtlAnagrafica();
            }
            $ana->load($this->objectMapping($data, 'anagrafica'));

            if (!$ana->save()) {
                Yii::info("Errore anagrafica " . $ana->getErrors(), 'sync');
                Yii::error($ana->getErrors());
                return false;
            //throw new \Exception("Errore salvataggio ".json_encode($ana->getErrors()));
            } else {
                if (!empty($data['contatto'])) {
                    $this->syncContatti($ana, $data['conAnagraficaContatto']);
                }
                if (!empty($data['indirizzo'])) {
                    $this->syncIndirizzi($ana, $data['indirizzo']);
                }

                $this->syncOrgDataWithAna($data);

                // ritorniamo l'anagrafica, potrebbe tornarci utile
            }
            return $ana;
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }

    /**
     * Aggiorna tutte le specializzazioni del volontario
     * @param  [type] $vol  [description]
     * @param  [type] $spec [description]
     * @return [type]       [description]
     */
    private function syncVolSpecializzazione($vol, $spec)
    {
        $vol->unlinkAll('specializzazione', true);
        foreach ($spec as $s) {
            $s_obj = $this->getSpecializzazione($s['id'], $s['descrizione']);
            if (!$s_obj) {
                $s_obj = new \common\models\UtlSpecializzazione();
                $s_obj->load($this->objectMapping($s, 'specializzazione'));
                if (!$s_obj->save()) {
                    Yii::error($s_obj->getErrors());
                    throw new \Exception("Errore salvataggio ".json_encode($s_obj->getErrors()));
                }
            }
            $vol->link('specializzazione', $s_obj);
        }
    }

    /**
     * Aggiorna tutte le specializzazioni dell'organizzazione
     * @param  [type] $org  [description]
     * @param  [type] $spec [description]
     * @return [type]       [description]
     */
    private function syncOrgSezioneSpecialistica($org, $spec)
    {
        $added_ids = [];

        foreach ($spec as $s) {
            //Yii::error( $s['descrizione'] );
            $s_obj = $this->getSezioneSpecialistica($s['id'], $s['descrizione']);
            if (!$s_obj) {
                $s_obj = new \common\models\TblSezioneSpecialistica();
                $s_obj->load($this->objectMapping($s, 'sezioneSpecialistica'));
                if (!$s_obj->save()) {
                    Yii::error($s_obj->getErrors());
                //throw new \Exception("Errore salvataggio ".json_encode($s_obj->getErrors()));
                } else {
                    $conn = new \common\models\ConOrganizzazioneSezioneSpecialistica();
                    $conn->id_organizzazione = $org->id;
                    $conn->id_sezione_specialistica = $s_obj->id;
                    if (!$conn->save()) {
                        Yii::error('Errore connessione sezione specialistica org ' . $org->id . ' sezione ' . $s_obj->id, 'sync');
                    } else {
                        $added_ids[] = $s_obj->id;
                    }
                }
            } else {
                $conn = \common\models\ConOrganizzazioneSezioneSpecialistica::find()->where(['id_organizzazione'=>$org->id])->andWhere(['id_sezione_specialistica'=>$s_obj->id])->one();
                if (!$conn) {
                    $conn = new \common\models\ConOrganizzazioneSezioneSpecialistica();
                    $conn->id_organizzazione = $org->id;
                    $conn->id_sezione_specialistica = $s_obj->id;
                    if (!$conn->save()) {
                        Yii::error('Errore connessione sezione specialistica org ' . $org->id . ' sezione ' . $s_obj->id, 'sync');
                    } else {
                        $added_ids[] = $s_obj->id;
                    }
                } else {
                    $added_ids[] = $s_obj->id;
                }
            }
        }


        $connections = \common\models\ConOrganizzazioneSezioneSpecialistica::find()
        ->where(['id_organizzazione'=>$org->id])
        ->andWhere(['not in', 'id_sezione_specialistica', $added_ids])
        ->all();
        foreach ($connections as $model_connection) {
            //Yii::error('Elimino ' . $model->connection->id_sezione_specialistica);
            $model_connection->delete();
        }
    }


    private function getCfValid($cf)
    {
        if (preg_match("/^RAND_/", $cf) || empty($cf)) {
            return null;
        }

        $ana = \common\models\UtlAnagrafica::find()->where(['codfiscale'=>$cf])->one();
        if ($ana) {
            return $ana->codfiscale;
        }

        return null;
    }

    /**
     * Parsing dei dati in entrata per formattazione utile a websor
     * @param  [type] $data [description]
     * @param  string $type [description]
     * @return [type]       [description]
     */
    private function objectMapping($data, $type = 'organizzazione')
    {
        switch ($type) {
            case 'organizzazione':
                $arr = [
                    'VolOrganizzazione'=> [
                        'denominazione' => (!empty($data['denominazione'])) ?
                        $data['denominazione'] : ((!empty($data['ragione_sociale'])) ? $data['ragione_sociale'] : "-"),
                        'codicefiscale' => $data['cf'],
                        'partita_iva' => $data['cf'],
                        'ref_id' => (!empty($data['num_elenco_territoriale'])) ? $data['num_elenco_territoriale'] : null,
                        'id_tipo_organizzazione' => $data['id_tipo_organizzazione'],
                        'ambito' => @$data['volOrganizzazione'][0]['ambito'],
                        //'tipo_albo_regionale' => '',
                        //'num_albo_regionale' => '',
                        //'data_albo_regionale' => '',
                        //'num_albo_provinciale' => '',
                        //'num_albo_nazionale' => '',
                        //'num_assicurazione' => '',
                        //'societa_assicurazione' => '',
                        //'data_scadenza_assicurazione' => '',
                        //'note' => '',
                        'data_costituzione' => $data['data_costituzione'],
                        //'ref_id' => $data['id']*-1,
                        //'email_responsabile' => $this->getContatti( @$data['rappresentanteLegale']['contatto'] , $this->contact_type_map['Email']),
                        //'pec_responsabile' => $this->getContatti( @$data['rappresentanteLegale']['contatto'] , $this->contact_type_map['Pec']),
                        'nome_responsabile' => @$data['rappresentanteLegale']['nome'] . " " . @$data['rappresentanteLegale']['cognome'],
                        //'tel_responsabile' => $this->getContatti( @$data['rappresentanteLegale']['contatto'] , $this->contact_type_map['Telefono']),
                        //'tel_referente' => $this->getContatti( @$data['referente']['contatto'] , $this->contact_type_map['Telefono']),
                        //'email_referente' => $this->getContatti( @$data['referente']['contatto'] , $this->contact_type_map['Email']),
                        //'fax_referente' => $this->getContatti( @$data['referente']['contatto'] , $this->contact_type_map['Fax']),
                        'nome_referente' => @$data['referente']['nome'] . " " . @$data['referente']['cognome'],
                        'stato_iscrizione' => (@$data['iscrizione']['stato']) ? @$data['iscrizione']['stato'] : 1,
                        // Sincronizzazione
                        'id_sync' => "MGO_".$data['id'],
                        // inserisco i seguenti
                        // per unire la referenza alle anagrafiche
                        // senza utilizzare id_sync
                        // disaccoppiamento
                    ]
                ];

                $rappr_cf = $this->getCfValid(@$data['rappresentanteLegale']['cf']);
                $ref_cf = $this->getCfValid(@$data['referente']['cf']);
                if ($rappr_cf) {
                    $arr['VolOrganizzazione']['cf_rappresentante_legale'] = $rappr_cf;
                }
                if ($ref_cf) {
                    $arr['VolOrganizzazione']['cf_referente'] = $ref_cf;
                }

                return $arr;

                break;
            case 'ente':
                $arr = [
                    'EntEnte'=> [
                        'denominazione' => (!empty($data['denominazione'])) ?
                        $data['denominazione'] : ((!empty($data['ragione_sociale'])) ? $data['ragione_sociale'] : "-"),
                        'codicefiscale' => $data['cf'],
                        'partita_iva' => $data['cf'],
                        'id_tipo_ente' => $data['id_tipo_ente'],
                        'id_sync' => "MGO_".$data['id']
                    ]
                ];

                $rappr_cf = $this->getCfValid(@$data['rappresentanteLegale']['cf']);
                $ref_cf = $this->getCfValid(@$data['referente']['cf']);
                if ($rappr_cf) {
                    $arr['EntEnte']['cf_rappresentante_legale'] = $rappr_cf;
                }
                if ($ref_cf) {
                    $arr['EntEnte']['cf_referente'] = $ref_cf;
                }

                return $arr;

                break;
            case 'struttura':
                $arr = [
                    'StrStruttura'=> [
                        'denominazione' => (!empty($data['denominazione'])) ?
                        $data['denominazione'] : ((!empty($data['ragione_sociale'])) ? $data['ragione_sociale'] : "-"),
                        'codicefiscale' => $data['cf'],
                        'partita_iva' => $data['cf'],
                        'id_tipo_struttura' => $data['id_tipo_struttura'],
                        'id_sync' => "MGO_".$data['id']
                    ]
                ];

                $rappr_cf = $this->getCfValid(@$data['rappresentanteLegale']['cf']);
                $ref_cf = $this->getCfValid(@$data['referente']['cf']);
                if ($rappr_cf) {
                    $arr['StrStruttura']['cf_rappresentante_legale'] = $rappr_cf;
                }
                if ($ref_cf) {
                    $arr['StrStruttura']['cf_referente'] = $ref_cf;
                }

                return $arr;

                break;
            case 'sede':
                $arr = [
                    'VolSede' => [
                        'name' => $data['name'],
                        'id_specializzazione' => null,
                        'tipo' => $data['tipo'],
                        'sitoweb' => null,
                        'cap' => $data['indirizzo']['cap'],
                        'disponibilita_oraria' => self::replaceDispH($data),
                        'id_sync' => "MGO_".$data['id']
                    ]
                ];


                $email = $this->getContatti(@$data['contatto'], $this->contact_type_map['Email']);
                $email_pec = $this->getContatti(@$data['contatto'], $this->contact_type_map['Pec']);
                $telefono = $this->getContatti(@$data['contatto'], $this->contact_type_map['Telefono']);
                $altro_telefono = $this->getContatti(@$data['contatto'], $this->contact_type_map['Tel h24']);
                $fax = $this->getContatti(@$data['contatto'], $this->contact_type_map['Fax']);
                $altro_fax = $this->getContatti(@$data['contatto'], $this->contact_type_map['Fax h24']);

                if (!empty($email)) {
                    $arr['VolSede']['email'] = $email;
                }
                if (!empty($email_pec)) {
                    $arr['VolSede']['email_pec'] = $email_pec;
                }
                if (!empty($telefono)) {
                    $arr['VolSede']['telefono'] = $telefono;
                }
                if (!empty($altro_telefono)) {
                    $arr['VolSede']['altro_telefono'] = $altro_telefono;
                }
                if (!empty($fax)) {
                    $arr['VolSede']['fax'] = $fax;
                }
                if (!empty($altro_fax)) {
                    $arr['VolSede']['altro_fax'] = $altro_fax;
                }

                if (!empty($data['id_organizzazione'])) {
                    $arr['VolSede']['id_organizzazione'] = $data['id_organizzazione'];
                }

                $c = false;
                if (!empty($data['lat']) && !empty($data['lon'])) {
                    $proj4 = new Proj4php();
                    $proj3003    = new Proj('+proj=tmerc +lat_0=0 +lon_0=9 +k=0.9996 +x_0=1500000 +y_0=0 +ellps=intl +units=m +no_defs', $proj4);
                    $projWGS84  = new Proj('EPSG:4326', $proj4);

                    $pointSrc = new Point($data['lon'], $data['lat'], $projWGS84);
                    $pointDest = $proj4->transform($proj3003, $pointSrc);

                    $c = $pointDest->toArray();

                    $arr['VolSede']['lat'] = $data['lat'];
                    $arr['VolSede']['lon'] = $data['lon'];
                    $arr['VolSede']['coord_x'] = $c[1];
                    $arr['VolSede']['coord_y'] = $c[0];
                }

                if (!empty($data['indirizzo']['indirizzo'])) {
                    $arr['VolSede']['indirizzo'] = $data['indirizzo']['indirizzo'];
                }

                if (!empty($data['indirizzo']['id_comune'])) {
                    $arr['VolSede']['comune'] = $data['indirizzo']['id_comune'];
                }

                return $arr;
                break;
            case 'ente_sede':
                $arr = [
                    'EntEnteSede' => [
                        'name' => $data['name'],
                        'id_ente' => $data['id_ente'],
                        'tipo' => $data['type'],
                        'cap' => $data['indirizzo']['cap'],
                        'id_sync' => "MGO_".$data['id']
                    ]
                ];

                $c = false;
                if (!empty($data['lat']) && !empty($data['lon'])) {
                    $proj4 = new Proj4php();
                    $proj3003    = new Proj('+proj=tmerc +lat_0=0 +lon_0=9 +k=0.9996 +x_0=1500000 +y_0=0 +ellps=intl +units=m +no_defs', $proj4);
                    $projWGS84  = new Proj('EPSG:4326', $proj4);

                    $pointSrc = new Point($data['lon'], $data['lat'], $projWGS84);
                    $pointDest = $proj4->transform($proj3003, $pointSrc);

                    $c = $pointDest->toArray();

                    $arr['EntEnteSede']['lat'] = $data['lat'];
                    $arr['EntEnteSede']['lon'] = $data['lon'];
                    $arr['EntEnteSede']['coord_x'] = $c[1];
                    $arr['EntEnteSede']['coord_y'] = $c[0];
                }

                if (!empty($data['indirizzo']['indirizzo'])) {
                    $arr['EntEnteSede']['indirizzo'] = $data['indirizzo']['indirizzo'];
                }

                if (!empty($data['indirizzo']['id_comune'])) {
                    $arr['EntEnteSede']['id_comune'] = $data['indirizzo']['id_comune'];
                }

                return $arr;
                break;
            case 'struttura_sede':
                $arr = [
                    'StrStrutturaSede' => [
                        'name' => $data['name'],
                        'id_struttura' => $data['id_struttura'],
                        'tipo' => $data['type'],
                        'cap' => $data['indirizzo']['cap'],
                        'id_sync' => "MGO_".$data['id']
                    ]
                ];

                $c = false;
                if (!empty($data['lat']) && !empty($data['lon'])) {
                    $proj4 = new Proj4php();
                    $proj3003    = new Proj('+proj=tmerc +lat_0=0 +lon_0=9 +k=0.9996 +x_0=1500000 +y_0=0 +ellps=intl +units=m +no_defs', $proj4);
                    $projWGS84  = new Proj('EPSG:4326', $proj4);

                    $pointSrc = new Point($data['lon'], $data['lat'], $projWGS84);
                    $pointDest = $proj4->transform($proj3003, $pointSrc);

                    $c = $pointDest->toArray();

                    $arr['StrStrutturaSede']['lat'] = $data['lat'];
                    $arr['StrStrutturaSede']['lon'] = $data['lon'];
                    $arr['StrStrutturaSede']['coord_x'] = $c[1];
                    $arr['StrStrutturaSede']['coord_y'] = $c[0];
                }

                if (!empty($data['indirizzo']['indirizzo'])) {
                    $arr['StrStrutturaSede']['indirizzo'] = $data['indirizzo']['indirizzo'];
                }

                if (!empty($data['indirizzo']['id_comune'])) {
                    $arr['StrStrutturaSede']['id_comune'] = $data['indirizzo']['id_comune'];
                }

                return $arr;
                break;
            case 'automezzo':
                $f =[
                    'targa',
                    'classe',
                    'sottoclasse',
                    'modello',
                    'data_immatricolazione',
                    'allestimento',
                    'capacita',
                    'tempo_attivazione'
                ];

                $data_ = [
                        'id_sync' => "MGO_".$data['id'],
                        'ref_id' => (!empty($data['ref_id'])) ? intval($data['ref_id']) : null,
                        'disponibilita' => "".$data['stato']
                    ];


                foreach ($f as $key) {
                    if ($this->getMetaRisorsaValue($data, $key)) {
                        $data_[$key] = $this->getMetaRisorsaValue($data, $key);
                    }
                }

                $data_['idorganizzazione'] = @$data['idorganizzazione'];
                $data_['idsede'] = @$data['idsede'];
                if (!empty($data['idtipo'])) {
                    $data_['idtipo'] = $data['idtipo'];
                }

                $data_['meta'] = [];
                try {
                    foreach ($data['risRisorsaMeta'] as $meta) {
                        $data_['meta'][$meta['meta']['key']] = $meta['meta_value'];
                    }
                } catch (\Exception $e) {
                    Yii::error('Errore aggiornamento meta', 'sync');
                }

                return [
                    'UtlAutomezzo' => $data_
                ];

                break;
            case 'attrezzatura':
                $f =[
                    'classe',
                    'sottoclasse',
                    'modello',
                    'allestimento',
                    'capacita',
                    'tempo_attivazione'
                ];

                $data_ = [
                        'id_sync' =>"MGO_". $data['id'],
                        'unita' => "".$data['quantita'],
                        'ref_id' => (!empty($data['ref_id'])) ? intval($data['ref_id']) : null,
                        'disponibilita' => "".$data['stato']
                    ];

                foreach ($f as $key) {
                    if ($this->getMetaRisorsaValue($data, $key)) {
                        $data_[$key] = $this->getMetaRisorsaValue($data, $key);
                    }
                }

                $data_['idorganizzazione'] = @$data['idorganizzazione'];
                $data_['idsede'] = @$data['idsede'];
                if (!empty($data['idtipo'])) {
                    $data_['idtipo'] = $data['idtipo'];
                }

                $data_['meta'] = [];
                try {
                    foreach ($data['risRisorsaMeta'] as $meta) {
                        $data_['meta'][$meta['meta']['key']] = $meta['meta_value'];
                    }
                } catch (\Exception $e) {
                    Yii::error('Errore aggiornamento meta', 'sync');
                }

                return [
                    'UtlAttrezzatura' => $data_
                ];
                break;
            case 'tipoAutomezzo':
                return [
                    'UtlAutomezzoTipo' => [
                        'descrizione' => $data['descrizione'],
                        'id_sync' => "MGO_".$data['id']
                    ]
                ];
                break;
            case 'tipoAttrezzatura':
                return [
                    'UtlAttrezzaturaTipo' => [
                        'descrizione' => $data['descrizione'],
                        'id_sync' => "MGO_".$data['id']
                    ]
                ];
                break;
            case 'tipoRisorsaMeta':
                return [
                    'TblTipoRisorsaMeta' => [
                        'type' => $data['type'],
                        'extra' => $data['extra'],
                        'key' => $data['key'],
                        'ref_id' => $data['ref_id'],
                        'label' => $data['label'],
                        'id_sync' => "MGO_".$data['id']
                    ]
                ];
                break;
            case 'tipoOrganizzazione':
                if ($data['elenco_territoriale'] == 1) {
                    return [
                        'VolTipoOrganizzazione' => [
                            'tipologia' => $data['descrizione'],
                            'elenco_territoriale' => $data['elenco_territoriale'],
                            'id_sync' => "MGO_".$data['id']
                        ]
                    ];
                } elseif ($data['ente_pubblico'] == 1) {
                    return [
                        'EntTipoEnte' => [
                            'descrizione' => $data['descrizione'],
                            'id_sync' => "MGO_".$data['id']
                        ]
                    ];
                } else {
                    return [
                        'StrTipoStruttura' => [
                            'descrizione' => $data['descrizione'],
                            'id_sync' => "MGO_".$data['id']
                        ]
                    ];
                }

                break;
            case 'tipoEnte':
                return [
                    'EntTipoEnte' => [
                        'descrizione' => $data['descrizione'],
                        'id_sync' => "MGO_".$data['id']
                    ]
                ];
                break;
            case 'tipoStruttura':
                return [
                    'StrTipoStruttura' => [
                        'descrizione' => $data['descrizione'],
                        'id_sync' => "MGO_".$data['id']
                    ]
                ];
                break;
            case 'anagrafica':
                return [
                    'UtlAnagrafica' => [
                        'id_sync' => "MGO_".$data['id'],
                        'nome' => $data['nome'],
                        'cognome' => $data['cognome'],
                        'codfiscale' => $data['cf'],
                        'luogo_nascita' => $data['luogo_nascita'],
                        'data_nascita' => $data['data_nascita'],
                        'comune_residenza' => @$data['residenza']['id_comune'],
                        'indirizzo_residenza' => @$data['residenza']['indirizzo'] . " " . @$data['residenza']['civico'],
                        'cap_residenza' => @$data['residenza']['cap']
                    ]
                ];
                break;
            case 'volontario':
                $data_ = [
                    'VolVolontario' => [
                        'id_sync' => "MGO_".$data['id'],
                        'valido_dal' => $data['data_start'],
                        'valido_al' => $data['data_end'],
                        'operativo' => $data['stato'],
                        'ruolo' => strtolower($data['ruolo'])
                    ]
                ];

                if (!empty($data['id_sede'])) {
                    $data_['VolVolontario']['id_sede'] = $data['id_sede'];
                }

                if (!empty($data['id_organizzazione'])) {
                    $data_['VolVolontario']['id_organizzazione'] = $data['id_organizzazione'];
                }

                if (!empty($data['id_anagrafica'])) {
                    $data_['VolVolontario']['id_anagrafica'] = $data['id_anagrafica'];
                }

                return $data_;
                break;
            case 'contatto':
                return [
                    'UtlContatto' => [
                        'id_sync' => "MGO_".$data['contatto']['id'],
                        'contatto' => $data['contatto']['contatto'],
                        'note' => @$data['contatto']['note'],
                        'check_mobile' => @$data['contatto']['check_mobile'],
                        'type' => $data['contatto']['type'],
                        //'use_type' => $data['use_type']
                    ]
                ];
                break;
            case 'indirizzo':
                return [
                    'UtlIndirizzo' => [
                        'id_sync' => "MGO_".$data['id'],
                        'indirizzo' => $data['indirizzo'],
                        'civico' => $data['civico'],
                        'cap' => $data['cap'],
                        'note' => $data['note'],
                        'id_comune' => $data['id_comune']
                    ]
                ];
                break;
            case 'specializzazione':
                return [
                    'UtlSpecializzazione' => [
                        'id_sync' => "MGO_".$data['id'],
                        'descrizione' => $data['descrizione']
                    ]
                ];
                break;
            case 'sezioneSpecialistica':
                return [
                    'TblSezioneSpecialistica' => [
                        'id_sync' => "MGO_".$data['id'],
                        'descrizione' => $data['descrizione']
                    ]
                ];
                break;
            case 'ruoloVolontario':
                return [
                    'TblRuoloVolontario' => [
                        'id_sync' => "MGO_".$data['id'],
                        'descrizione' => $data['descrizione']
                    ]
                ];
                break;
        }
    }



    public function created_organizzazione()
    {
        $this->org_update();
    }

    public function updated_organizzazione()
    {
        $this->org_update();
    }

    private function org_update()
    {
        $org = $this->getOrganizzazione($this->message['data']['id'], $this->message['data']['cf'], $this->message['data']['num_elenco_territoriale']);

        if (!$org) {
            $org = new \common\models\VolOrganizzazione();
        }

        $tipo_org = $this->getTipoOrganizzazione(
            $this->message['data']['tipoOrganizzazione']['id'],
            $this->message['data']['tipoOrganizzazione']['descrizione']
        );
        if (!$tipo_org) {
            $tipo_org = new \common\models\VolTipoOrganizzazione();
            $tipo_org->load($this->objectMapping($this->message['data']['tipoOrganizzazione'], 'tipoOrganizzazione'));
            if (!$tipo_org->save()) {
                Yii::error($tipo_org->getErrors());
                throw new \Exception("Errore salvataggio ".json_encode($tipo_org->getErrors()));
            }
        }
        $this->message['data']['id_tipo_organizzazione'] = $tipo_org->id;

        $org->load($this->objectMapping($this->message['data']));
        if (!$org->save()) {
            Yii::error($org->getErrors());
            throw new \Exception("Errore salvataggio ".json_encode($org->getErrors()));
        }

        $this->syncContatti($org, $this->message['data']['conOrganizzazioneContatto']);

        // sincronizziamo i dati delle anagrafiche di referente e rappr legale per sicurezza
        if (!empty($this->message['data']['rappresentanteLegale'])) {
            $this->syncAnagrafica($this->message['data']['rappresentanteLegale']);
        }
        if (!empty($this->message['data']['referente'])) {
            $this->syncAnagrafica($this->message['data']['referente']);
        }


        if (!empty($this->message['data']['volOrganizzazione'][0]['sezioneSpecialistica'])) :
            $this->syncOrgSezioneSpecialistica($org, $this->message['data']['volOrganizzazione'][0]['sezioneSpecialistica']);
        else :
            $this->syncOrgSezioneSpecialistica($org, []);
        endif;

        if ($org->stato_iscrizione == \common\models\VolOrganizzazione::STATO_ATTIVA) {
            $org->syncEverbridge();
        } else {
            $org->removeFromEverbridge();
        }
    }

    public function deleted_organizzazione()
    {
        $org = \common\models\VolOrganizzazione::find()->where([ 'id_sync'=>'MGO_'.$this->message['data']['id'] ])->one();
        if (!$org) {
            return;
        }

        $org->removeFromEverbridge();

        if (!$org->delete()) {
            Yii::error($org->getErrors(), 'sync');
            throw new \Exception("Errore eliminazione ".json_encode($org->getErrors()));
        }
    }



    public function created_ente()
    {
        $this->ente_update();
    }

    public function updated_ente()
    {
        $this->ente_update();
    }

    private function ente_update()
    {
        $org = $this->getEnte($this->message['data']['id'], $this->message['data']['cf'], $this->message['data']['ref_id']);
        if (!$org) {
            $org = new \common\models\ente\EntEnte();
        }

        $tipo_org = $this->getTipoEnte($this->message['data']['tipoOrganizzazione']['id'], $this->message['data']['tipoOrganizzazione']['descrizione']);
        if (!$tipo_org) {
            $tipo_org = new \common\models\ente\EntTipoEnte();
            $tipo_org->load($this->objectMapping($this->message['data']['tipoOrganizzazione'], 'tipoEnte'));
            if (!$tipo_org->save()) {
                Yii::error($tipo_org->getErrors());
                throw new \Exception("Errore salvataggio ".json_encode($tipo_org->getErrors()));
            }
        }

        $this->message['data']['id_tipo_ente'] = $tipo_org->id;

        $org->load($this->objectMapping($this->message['data'], 'ente'));
        if (!$org->save()) {
            Yii::error($this->objectMapping($this->message['data'], 'ente'));
            throw new \Exception("Errore salvataggio ".json_encode($org->getErrors()));
        }

        $this->syncContatti($org, $this->message['data']['conOrganizzazioneContatto']);

        // sincronizziamo i dati delle anagrafiche di referente e rappr legale per sicurezza
        if (!empty($this->message['data']['rappresentanteLegale'])) {
            $this->syncAnagrafica($this->message['data']['rappresentanteLegale']);
        }
        if (!empty($this->message['data']['referente'])) {
            $this->syncAnagrafica($this->message['data']['referente']);
        }


        $org->syncEverbridge();
    }

    public function deleted_ente()
    {
        $org = \common\models\ente\EntEnte::find()->where([ 'id_sync'=>'MGO_'.$this->message['data']['id'] ])->one();
        if (!$org) {
            return;
        }

        $org->removeFromEverbridge();
        if (!$org->delete()) {
            Yii::error($org->getErrors(), 'sync');
            throw new \Exception("Errore eliminazione ".json_encode($org->getErrors()));
        }
    }



    public function created_struttura()
    {
        $this->struttura_update();
    }

    public function updated_struttura()
    {
        $this->struttura_update();
    }

    private function struttura_update()
    {
        $org = $this->getStruttura($this->message['data']['id'], $this->message['data']['cf'], $this->message['data']['ref_id']);
        if (!$org) {
            $org = new \common\models\struttura\StrStruttura();
        }

        $tipo_org = $this->getTipoStruttura($this->message['data']['tipoOrganizzazione']['id'], $this->message['data']['tipoOrganizzazione']['descrizione']);
        if (!$tipo_org) {
            $tipo_org = new \common\models\struttura\StrTipoStruttura();
            $tipo_org->load($this->objectMapping($this->message['data']['tipoOrganizzazione'], 'tipoStruttura'));
            if (!$tipo_org->save()) {
                Yii::error($tipo_org->getErrors());
                throw new \Exception("Errore salvataggio ".json_encode($tipo_org->getErrors()));
            }
        }
        $this->message['data']['id_tipo_struttura'] = $tipo_org->id;

        $org->load($this->objectMapping($this->message['data'], 'struttura'));
        if (!$org->save()) {
            Yii::error($org->getErrors());
            throw new \Exception("Errore salvataggio ".json_encode($org->getErrors()));
        }

        $this->syncContatti($org, $this->message['data']['conOrganizzazioneContatto']);

        // sincronizziamo i dati delle anagrafiche di referente e rappr legale per sicurezza
        if (!empty($this->message['data']['rappresentanteLegale'])) {
            $this->syncAnagrafica($this->message['data']['rappresentanteLegale']);
        }
        if (!empty($this->message['data']['referente'])) {
            $this->syncAnagrafica($this->message['data']['referente']);
        }

        $org->syncEverbridge();
    }

    public function deleted_struttura()
    {
        $org = \common\models\struttura\StrStruttura::find()->where([ 'id_sync'=>'MGO_'.$this->message['data']['id'] ])->one();
        if (!$org) {
            return;
        }

        $org->removeFromEverbridge();

        if (!$org->delete()) {
            Yii::error($org->getErrors(), 'sync');
            throw new \Exception("Errore eliminazione ".json_encode($org->getErrors()));
        }
    }




    public function created_risorsa()
    {
        $this->risorsa_update();
    }

    public function updated_risorsa()
    {
        $this->risorsa_update();
    }

    private function risorsa_update()
    {
        /**
         * In base a mappatura scegliamo se aggiornare un automezzo o una attrezzatura
         */
        if (empty($this->message['data']['tipoRisorsa']['categoria'])) {
            Yii::info('no cat', 'sync');
            return;
        }
        // metto di default automezzo per import customizzato
        $type = (!empty(@$this->map_risorsa_type[$this->message['data']['tipoRisorsa']['categoria']])) ? $this->map_risorsa_type[$this->message['data']['tipoRisorsa']['categoria']] : 'automezzo';

        switch ($type) {
            case 'automezzo':
                $this->update_automezzo();
                break;
            case 'attrezzatura':
                $this->update_attrezzatura();
                break;
        }
    }

    private function update_automezzo()
    {
        $type = $this->getTipoRisorsa($this->message['data']['tipoRisorsa']['id'], $this->message['data']['tipoRisorsa']['descrizione'], 'automezzo');
        if (!$type) {
            $type = new \common\models\UtlAutomezzoTipo();
        }
        $type->load($this->objectMapping($this->message['data']['tipoRisorsa'], 'tipoAutomezzo'));
        if (!$type->save()) {
            Yii::error($type->getErrors(), 'sync');
            throw new \Exception("Errore salvataggio ".json_encode($type->getErrors()));
        }

        $this->message['data']['idtipo'] = $type->id;

        $automezzo = $this->getRisorsa($this->message['data']['id'], intval($this->message['data']['ref_id']));
        if (!$automezzo) {
            $automezzo = new \common\models\UtlAutomezzo();
        }

        /**
         * Prendo sede e organizzazione
         */
        $org = $this->getOrganizzazione(
            $this->message['data']['organizzazione']['id'],
            $this->message['data']['organizzazione']['cf'],
            $this->message['data']['organizzazione']['num_elenco_territoriale']
        );
        if (!empty($org)) {
            $sede = $this->getSede($this->message['data']['sede'], $org->id);
            if (!$sede) {
                unset($this->message['data']['sede']['id']);
            }

            if (!empty($sede)) :
                $this->message['data']['idsede'] = $sede->id;
            endif;
            $this->message['data']['idorganizzazione'] = $org->id;
        } else {
            if (!empty($this->message['data']['organizzazione']['num_elenco_territoriale'])) {
                return;
            }
            // per gestire le risorse che passano a regione
            // non potendo determinare se struttura o ente per evitare che ci siano associazioni errate
            // mettiamo che non ha organizzazione
            // 29/04/2020
            $this->message['data']['idorganizzazione'] = null;
            $this->message['data']['idsede'] = null;
        }

        $automezzo->load($this->objectMapping($this->message['data'], 'automezzo'));
        if (!$automezzo->save()) {
            Yii::error($automezzo->getErrors(), 'sync');
            throw new \Exception("Errore salvataggio ".json_encode($automezzo->getErrors()));
        }
    }

    private function update_attrezzatura()
    {
        $type = $this->getTipoRisorsa($this->message['data']['tipoRisorsa']['id'], $this->message['data']['tipoRisorsa']['descrizione'], 'attrezzatura');
        if (!$type) {
            $type = new \common\models\UtlAttrezzaturaTipo();
        }
        $type->load($this->objectMapping($this->message['data']['tipoRisorsa'], 'tipoAttrezzatura'));
        $type->save();
        if (!$type->save()) {
            Yii::error($type->getErrors(), 'sync');
            throw new \Exception("Errore salvataggio ".json_encode($type->getErrors()));
        }

        $this->message['data']['idtipo'] = $type->id;

        $attrezzatura = $this->getRisorsa($this->message['data']['id'], intval($this->message['data']['ref_id']));

        if (!$attrezzatura) {
            $attrezzatura = new \common\models\UtlAttrezzatura();
        }

        /**
         * Prendo sede e organizzazione
         */
        $org = $this->getOrganizzazione($this->message['data']['organizzazione']['id'], $this->message['data']['organizzazione']['cf'], $this->message['data']['organizzazione']['num_elenco_territoriale']);
        if (!empty($org)) {
            $sede = $this->getSede($this->message['data']['sede'], $org->id);
            if (!$sede) {
                unset($this->message['data']['sede']['id']);
            }


            if (!empty($sede)) :
                $this->message['data']['idsede'] = $sede->id;
            endif;
            $this->message['data']['idorganizzazione'] = $org->id;
        } else {
            if (!empty($this->message['data']['organizzazione']['num_elenco_territoriale'])) {
                return;
            }
            // per gestire le risorse che passano a regione
            // non potendo determinare se struttura o ente per evitare che ci siano associazioni errate
            // mettiamo che non ha organizzazione
            // 29/04/2020
            $this->message['data']['idorganizzazione'] = null;
            $this->message['data']['idsede'] = null;
        }

        $attrezzatura_data = $this->objectMapping($this->message['data'], 'attrezzatura');

        if (!isset($attrezzatura_data['UtlAttrezzatura']['modello'])) {
            $attrezzatura_data['UtlAttrezzatura']['modello'] = '-';
        }

        $attrezzatura->load($attrezzatura_data);
        if (!$attrezzatura->save()) {
            Yii::error($attrezzatura->getErrors(), 'sync');
            throw new \Exception("Errore salvataggio ".json_encode($attrezzatura->getErrors()), 1);
        }
    }

    public function deleted_risorsa()
    {
        $ris = $this->getRisorsa($this->message['data']['id'], intval($this->message['data']['ref_id']));
        if ($ris) {
            $ris->delete();
        }
    }

    public function created_sede()
    {
        $this->sede_update();
    }

    public function updated_sede()
    {
        $this->sede_update();
    }

    private function sede_update()
    {
        $org = $this->getOrganizzazione($this->message['data']['organizzazione']['id'], $this->message['data']['organizzazione']['cf'], $this->message['data']['organizzazione']['num_elenco_territoriale']);
        if (!$org) {
            throw new \Exception("Organizzazione non presente", 1);
        }

        // uso l'id organizzazione di websor
        $this->message['data']['id_organizzazione'] = $org->id;

        $sede = $this->getSede($this->message['data'], $org->id);
        if (!$sede) {
            $sede = new \common\models\VolSede();
        }

        $sede->load($this->objectMapping($this->message['data'], 'sede'));
        // fallback ingestibile altrimenti
        if ($sede->indirizzo == '') {
            $sede->indirizzo = ' - ';
        }
        if (!$sede->save()) {
            Yii::error($sede->getErrors());
            throw new \Exception("Errore salvataggio sede organizzazione " . $org->id . " ".json_encode($sede->getErrors()));
        }

        $this->syncContatti($sede, $this->message['data']['conSedeContatto']);
    }

    public function deleted_sede()
    {
        $sede = \common\models\VolSede::find()->where([ 'id_sync'=>'MGO_'.$this->message['data']['id'] ])->one();
        if (!$sede) {
            return;
        }
        if (!$sede->delete()) {
            Yii::error($sede->getErrors());
            throw new \Exception("Errore eliminazione ".json_encode($sede->getErrors()));
        }
    }



    public function created_ente_sede()
    {
        $this->ente_sede_update();
    }

    public function updated_ente_sede()
    {
        $this->ente_sede_update();
    }

    private function ente_sede_update()
    {
        $org = $this->getEnte($this->message['data']['organizzazione']['id'], $this->message['data']['organizzazione']['cf']);
        if (!$org) {
            throw new \Exception("Organizzazione non presente", 1);
        }

        // uso l'id organizzazione di websor
        $this->message['data']['id_ente'] = $org->id;

        $sede = $this->getEnteSede($this->message['data'], $org->id);
        if (!$sede) {
            $sede = new \common\models\ente\EntEnteSede();
        }

        $sede->load($this->objectMapping($this->message['data'], 'ente_sede'));
        // fallback ingestibile altrimenti
        if ($sede->indirizzo == '') {
            $sede->indirizzo = ' - ';
        }
        if (!$sede->save()) {
            Yii::error($sede->getErrors());
            throw new \Exception("Errore salvataggio sede ente " . $org->id . " ".json_encode($sede->getErrors()));
        }

        $this->syncContatti($sede, $this->message['data']['conSedeContatto']);
    }

    public function deleted_ente_sede()
    {
        $sede = \common\models\ente\EntEnteSede::find()->where([ 'id_sync'=>'MGO_'.$this->message['data']['id'] ])->one();
        if (!$sede) {
            return;
        }
        if (!$sede->delete()) {
            Yii::error($sede->getErrors());
            throw new \Exception("Errore eliminazione ".json_encode($sede->getErrors()));
        }
    }

    public function created_struttura_sede()
    {
        $this->struttura_sede_update();
    }

    public function updated_struttura_sede()
    {
        $this->struttura_sede_update();
    }

    private function struttura_sede_update()
    {
        $org = $this->getStruttura($this->message['data']['organizzazione']['id'], $this->message['data']['organizzazione']['cf']);
        if (!$org) {
            throw new \Exception("Organizzazione non presente", 1);
        }

        // uso l'id organizzazione di websor
        $this->message['data']['id_struttura'] = $org->id;

        $sede = $this->getStrutturaSede($this->message['data'], $org->id);
        // fallback ingestibile altrimenti

        if (!$sede) {
            $sede = new \common\models\struttura\StrStrutturaSede();
        }

        $sede->load($this->objectMapping($this->message['data'], 'struttura_sede'));
        if ($sede->indirizzo == '') {
            $sede->indirizzo = ' - ';
        }
        if (!$sede->save()) {
            Yii::error($sede->getErrors());
            throw new \Exception("Errore salvataggio sede struttura " . $org->id . " ".json_encode($sede->getErrors()));
        }

        $this->syncContatti($sede, $this->message['data']['conSedeContatto']);
    }

    public function deleted_struttura_sede()
    {
        $sede = \common\models\struttura\StrStrutturaSede::find()->where([ 'id_sync'=>'MGO_'.$this->message['data']['id'] ])->one();
        if (!$sede) {
            return;
        }
        if (!$sede->delete()) {
            Yii::error($sede->getErrors());
            throw new \Exception("Errore eliminazione ".json_encode($sede->getErrors()));
        }
    }

    /**
     * Aggiornato volontario secco (datori di lavoro)
     *
     */
    public function updated_anagraficavolontario()
    {
        $id_anagrafica = @$this->message['data']['anagrafica']['id'];
        $cf = @$this->message['data']['anagrafica']['cf'];
        if (!$id_anagrafica) {
            return;
        }

        $ana = \common\models\UtlAnagrafica::find()
            ->where(['id_sync'=>"MGO_".$id_anagrafica])
            ->orWhere(['codfiscale'=>$cf])
            ->all();

        $ids = [];
        foreach ($ana as $a) {
            $ids[] = $a->id;
        }

        if (count($ids) <= 0) {
            return;
        }

        Yii::$app->db->createCommand()
            ->update(
                \common\models\VolVolontario::tableName(),
                ['datore_di_lavoro' => $this->message['data']['datore_di_lavoro']],
                [ 'id_anagrafica' => $ids]
            )->execute();
    }

    public function created_volontario()
    {
        $this->volontario_update();
    }

    public function updated_volontario()
    {
        $this->volontario_update();
    }

    private function volontario_update()
    {
        // mi servono sede organizzazione e anagrafica
        $org = $this->getOrganizzazione($this->message['data']['organizzazione']['id'], $this->message['data']['organizzazione']['cf'], $this->message['data']['organizzazione']['num_elenco_territoriale']);
        if (!$org) {
            Yii::error("Organizzazione non trovata ".$this->message['data']['organizzazione']['cf']);
            throw new \Exception("Errore organizzazione non trovata ".$this->message['data']['organizzazione']['cf']);
        }

        $sede = $this->getSede($this->message['data']['sede'], $org->id);

        $this->message['data']['id_organizzazione'] = $org->id;
        $this->message['data']['id_sede'] = ($sede) ? $sede->id : null;

        // cerco o aggiungo l'anagrafica corrispondente
        $ana = $this->syncAnagrafica($this->message['data']['volontario']['anagrafica']);
        // se non la trovo o va in errore l'inserimento è un bel problema
        if (!$ana) {
            Yii::error("Errore anagrafica volontario ".$this->message['data']['id']);
        //throw new \Exception("Errore anagrafica volontario ".$this->message['data']['id']);
        } else {
            $this->message['data']['id_anagrafica'] = $ana->id;

            $vol = \common\models\VolVolontario::find()
            ->where(['id_sync'=>'MGO_'.$this->message['data']['id']])
            ->one();

            if (!$vol) {
                $vol = \common\models\VolVolontario::find()
                ->where(['id_organizzazione'=>$org->id])
                ->andWhere(['id_anagrafica'=>$ana->id])
                ->one();
            }

            if (!$vol) {
                $vol = new \common\models\VolVolontario();
            }

            $vol->load($this->objectMapping($this->message['data'], 'volontario'));
            if (!$vol->save()) {
                Yii::error($vol->getErrors());
                throw new \Exception("Errore salvataggio ".json_encode($vol->getErrors()));
            }

            // ora possiamo sincronizzare anche contatti e indirizzi del volontario
            $this->syncContatti($vol, $this->message['data']['conVolontarioContatto']);
            $this->syncIndirizzi($vol, $this->message['data']['indirizzo']);

            if (!empty($this->message['data']['volontario']['specializzazione'])) :
                $this->syncVolSpecializzazione($vol, $this->message['data']['volontario']['specializzazione']);
            else :
                $this->syncVolSpecializzazione($vol, []);
            endif;
        }
    }

    public function deleted_volontario()
    {
        $vol = \common\models\VolVolontario::find()->where(['id_sync'=>"MGO_".$this->message['data']['id']])->one();
        if ($vol) {
            $vol->delete();
        }
    }


    public function created_anagrafica()
    {
        $this->anagrafica_update();
    }

    public function updated_anagrafica()
    {
        $this->anagrafica_update();
    }

    private function anagrafica_update()
    {
        $this->syncAnagrafica($this->message['data']);
    }

    public function deleted_anagrafica()
    {
        $ana = \common\models\UtlAnagrafica::find()->where(['id_sync'=>"MGO_".$this->message['data']['id']])->one();
        if ($ana) {
            $ana->delete();
        }
    }


    public function created_tipoOrganizzazione()
    {
        $this->tipoOrganizzazione_updated();
    }

    public function updated_tipoOrganizzazione()
    {
        $this->tipoOrganizzazione_updated();
    }

    private function tipoOrganizzazione_updated()
    {
        $data = $this->message['data'];

        $get_action = 'getTipoStruttura';
        $map_obj = 'tipoStruttura';
        $obj = 'common\models\struttura\StrTipoStruttura';

        if (!empty($data['elenco_territoriale']) && $data['elenco_territoriale'] == 1) {
            $get_action = 'getTipoOrganizzazione';
            $map_obj = 'tipoOrganizzazione';
            $obj = 'common\models\VolTipoOrganizzazione';
        }
        if (!empty($data['ente_pubblico']) && $data['ente_pubblico'] == 1) {
            $get_action = 'getTipoEnte';
            $map_obj = 'tipoEnte';
            $obj = 'common\models\ente\EntTipoEnte';
        }

        $t = $this->$get_action($data['id'], $data['descrizione']);
        if (!$t) {
            $t = new $obj();
        }

        $t->load($this->objectMapping($data, $map_obj));
        if (!$t->save()) {
            Yii::error($t->getErrors());
            throw new \Exception("Errore salvataggio ".json_encode($t->getErrors()));
        }
    }

    public function deleted_tipoOrganizzazione()
    {
        $data = $this->message['data'];

        $get_action = 'getTipoStruttura';
        $map_obj = 'tipoStruttura';

        if (!empty($data['elenco_territoriale']) && $data['elenco_territoriale'] == 1) {
            $get_action = 'getTipoOrganizzazione';
            $map_obj = 'tipoOrganizzazione';
        }
        if (!empty($data['ente_pubblic']) && $data['ente_pubblic'] == 1) {
            $get_action = 'getTipoEnte';
            $map_obj = 'tipoEnte';
        }

        $t = $this->$get_action($data['id'], $data['descrizione']);
        if ($t) {
            $t->delete();
        }
    }


    public function created_tipoRisorsa()
    {
        $this->tipoRisorsa_update();
    }

    public function updated_tipoRisorsa()
    {
        $this->tipoRisorsa_update();
    }

    private function tipoRisorsa_update()
    {
        /**
         * In base a mappatura scegliamo se aggiornare un automezzo o una attrezzatura
         */
        if (empty($this->message['data']['categoria'])) {
            return;
        }
        $type = (!empty(@$this->map_risorsa_type[$this->message['data']['categoria']])) ? $this->map_risorsa_type[strtoupper($this->message['data']['categoria'])] : 'automezzo';

        $tipo_risorsa = $this->getTipoRisorsa($this->message['data']['id'], $this->message['data']['descrizione'], $type);
        if (!$tipo_risorsa) {
            $tipo_risorsa = ($type == 'automezzo') ? new \common\models\UtlAutomezzoTipo() : new \common\models\UtlAttrezzaturaTipo();
        }

        $mapping_type = ($type == 'automezzo') ? 'tipoAutomezzo' : 'tipoAttrezzatura';


        $tipo_risorsa->load($this->objectMapping($this->message['data'], $mapping_type));
        if (!$tipo_risorsa->save()) {
            Yii::error($tipo_risorsa->getErrors());
            throw new \Exception("Errore salvataggio ".json_encode($tipo_risorsa->getErrors()));
        }
    }

    public function deleted_tipoRisorsa()
    {
        if (empty($this->message['data']['categoria'])) {
            return;
        }
        $type = $this->map_risorsa_type[$this->message['data']['categoria']];

        $tipo_risorsa = $this->getTipoRisorsa($this->message['data']['id'], $this->message['data']['descrizione'], $type);
        if ($tipo_risorsa) {
            $tipo_risorsa->delete();
        }
    }




    public function created_tipoRisorsaMeta()
    {
        $this->tipoRisorsaMeta_update();
    }

    public function updated_tipoRisorsaMeta()
    {
        $this->tipoRisorsaMeta_update();
    }

    private function tipoRisorsaMeta_update()
    {
        $tipo_risorsa_meta = $this->getTipoRisorsaMeta($this->message['data']['id'], $this->message['data']['key']);
        if (!$tipo_risorsa_meta) {
            $tipo_risorsa_meta = new \common\models\tabelle\TblTipoRisorsaMeta();
        }

        $tipo_risorsa_meta->load($this->objectMapping($this->message['data'], 'tipoRisorsaMeta'));
        if (!$tipo_risorsa_meta->save()) {
            Yii::error($tipo_risorsa_meta->getErrors());
            throw new \Exception("Errore salvataggio ".json_encode($tipo_risorsa_meta->getErrors()));
        }
    }

    public function deleted_tipoRisorsaMeta()
    {
        $tipo_risorsa_meta = $this->getTipoRisorsaMeta($this->message['data']['id'], $this->message['data']['key']);
        if ($tipo_risorsa_meta) {
            $tipo_risorsa_meta->delete();
        }
    }







    public function deleted_contatto()
    {
        $c = \common\models\utility\UtlContatto::find()->where(['id_sync'=>"MGO_".$this->message['data']['id']])->one();
        if ($c) {
            if (!$c->delete()) {
                Yii::error("Impossibile eliminare contatto ".$c->id);
            }
        }
    }

    public function deleted_indirizzo()
    {
        $i = \common\models\utility\UtlIndirizzo::find()->where(['id_sync'=>"MGO_".$this->message['data']['id']])->one();
        if ($i) {
            if (!$i->delete()) {
                Yii::error("Impossibile eliminare contatto ".$i->id);
            }
        }
    }


    public function created_specializzazione()
    {
        $this->specializzazione_updated();
    }

    public function updated_specializzazione()
    {
        $this->specializzazione_updated();
    }

    private function specializzazione_updated()
    {
        $t = $this->getSpecializzazione($this->message['data']['id'], $this->message['data']['descrizione']);
        if (!$t) {
            $t = new \common\models\UtlSpecializzazione();
        }

        $t->load($this->objectMapping($this->message['data'], 'specializzazione'));
        if (!$t->save()) {
            Yii::error($t->getErrors());
            throw new \Exception("Errore salvataggio ".json_encode($t->getErrors()));
        }
    }

    public function deleted_specializzazione()
    {
        $t = $this->getSpecializzazione($this->message['data']['id'], $this->message['data']['descrizione']);
        if ($t) {
            $t->delete();
        }
    }


    public function created_sezioneSpecialistica()
    {
        $this->sezioneSpecialistica_updated();
    }

    public function updated_sezioneSpecialistica()
    {
        $this->sezioneSpecialistica_updated();
    }

    private function sezioneSpecialistica_updated()
    {
        $t = $this->getSezioneSpecialistica($this->message['data']['id'], $this->message['data']['descrizione']);
        if (!$t) {
            $t = new \common\models\TblSezioneSpecialistica();
        }

        $t->load($this->objectMapping($this->message['data'], 'sezioneSpecialistica'));
        if (!$t->save()) {
            Yii::error($t->getErrors());
            throw new \Exception("Errore salvataggio ".json_encode($t->getErrors()));
        }
    }

    public function deleted_sezioneSpecialistica()
    {
        $t = $this->getTipoOrganizzazione($this->message['data']['id'], $this->message['data']['descrizione']);
        if ($t) {
            $t->delete();
        }
    }



    public function created_ruoloVolontario()
    {
        $this->ruoloVolontario_updated();
    }

    public function updated_ruoloVolontario()
    {
        $this->ruoloVolontario_updated();
    }

    private function ruoloVolontario_updated()
    {
        $t = $this->getRuoloVolontario($this->message['data']['id'], $this->message['data']['descrizione']);

        if (!$t) {
            $t = new \common\models\TblRuoloVolontario();
        }

        $t->load($this->objectMapping($this->message['data'], 'ruoloVolontario'));
        if (!$t->save()) {
            Yii::error($t->getErrors());
            throw new \Exception("Errore salvataggio ".json_encode($t->getErrors()));
        }
    }

    public function deleted_ruoloVolontario()
    {
        $t = $this->getTipoOrganizzazione($this->message['data']['id'], $this->message['data']['descrizione']);
        if ($t) {
            $t->delete();
        }
    }
}
