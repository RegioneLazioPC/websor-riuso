<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{

    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $admin = $auth->createRole('Dirigente');
        $auth->add($admin);

                
        $adminPermission = $auth->createPermission('adminPermission');
        $auth->add($adminPermission);

        $assignEvento = $auth->createPermission('assignEvento');
        $auth->add($assignEvento);

        $assignEventoByProvincia = $auth->createPermission('assignEventoByProvincia');
        $auth->add($assignEventoByProvincia);

        $controlroomPermission = $auth->createPermission('controlroomPermission');
        $auth->add($controlroomPermission);

        $createEvento = $auth->createPermission('createEvento');
        $auth->add($createEvento);

        $createSegnalazione = $auth->createPermission('createSegnalazione');
        $auth->add($createSegnalazione);

        $createTaskEvento = $auth->createPermission('createTaskEvento');
        $auth->add($createTaskEvento);



        $funzionarioSOP = $auth->createRole('Volontario');
        $auth->add($funzionarioSOP);

        $funzionarioSOR = $auth->createRole('Funzionario');
        $auth->add($funzionarioSOR);


        $gestioneIncendio = $auth->createPermission('gestioneIncendio');
        $auth->add($gestioneIncendio);

        $listEvento = $auth->createPermission('listEvento');
        $auth->add($listEvento);

        $listSegnalazioni = $auth->createPermission('listSegnalazioni');
        $auth->add($listSegnalazioni);

        $listSegnalazioniEvento = $auth->createPermission('listSegnalazioniEvento');
        $auth->add($listSegnalazioniEvento);

        $manageEvento = $auth->createPermission('manageEvento');
        $auth->add($manageEvento);

        $manageSegnalazione = $auth->createPermission('manageSegnalazione');
        $auth->add($manageSegnalazione);

        $manageTaskEvento = $auth->createPermission('manageTaskEvento');
        $auth->add($manageTaskEvento);


        $operatore = $auth->createRole('Operatore');
        $auth->add($operatore);

        $operatorePrefettura = $auth->createRole('VF');
        $auth->add($operatorePrefettura);


        $publicEvento = $auth->createPermission('publicEvento');
        $auth->add($publicEvento);

        $updateEvento = $auth->createPermission('updateEvento');
        $auth->add($updateEvento);

        $viewEvento = $auth->createPermission('viewEvento');
        $auth->add($viewEvento);



        $auth->addChild($admin, $adminPermission);
        $auth->addChild($admin, $funzionarioSOR);
        $auth->addChild($admin, $funzionarioSOP);

        $auth->addChild($assignEventoByProvincia, $assignEvento);

        $auth->addChild($funzionarioSOP, $assignEventoByProvincia);
        $auth->addChild($funzionarioSOP, $createTaskEvento);
        $auth->addChild($funzionarioSOP, $listEvento);
        $auth->addChild($funzionarioSOP, $manageSegnalazione);
        $auth->addChild($funzionarioSOP, $viewEvento);
        $auth->addChild($funzionarioSOR, $manageEvento);
        $auth->addChild($funzionarioSOR, $manageSegnalazione);
        $auth->addChild($funzionarioSOR, $manageTaskEvento);

        $auth->addChild($gestioneIncendio, $listSegnalazioni);

        $auth->addChild($manageEvento, $assignEvento);
        $auth->addChild($manageEvento, $createEvento);
        $auth->addChild($manageEvento, $createTaskEvento);
        $auth->addChild($manageEvento, $listEvento);
        $auth->addChild($manageEvento, $listSegnalazioniEvento);
        $auth->addChild($manageEvento, $publicEvento);
        $auth->addChild($manageEvento, $updateEvento);
        $auth->addChild($manageEvento, $viewEvento);

        $auth->addChild($manageSegnalazione, $createSegnalazione);
        $auth->addChild($manageSegnalazione, $listSegnalazioni);


        $auth->addChild($operatore, $createTaskEvento);
        $auth->addChild($operatore, $listEvento);
        $auth->addChild($operatore, $listSegnalazioniEvento);
        $auth->addChild($operatore, $manageSegnalazione);

        $auth->addChild($operatorePrefettura, $listEvento);
        $auth->addChild($operatorePrefettura, $viewEvento);

        
    }

    /**
     * Alberatura assegnazione singolo permesso a ruolo
     * @var [type]
     */
    private $permissions_map = [
        'createSegnalazione' => ['Operatore','Dirigente','Funzionario','Volontario','VF'],
        'viewSegnalazione' => ['Operatore','Dirigente','Funzionario','Volontario','VF'],
        'listSegnalazioni' => ['Operatore','Dirigente','Funzionario','Volontario','VF'],
        'updateSegnalazione' => ['Operatore','Dirigente','Funzionario','Volontario','VF'],
        'closeSegnalazione' => ['Operatore','Dirigente','Funzionario','VF'],
        'transformSegnalazioneToEvento' => ['Operatore','Dirigente','Funzionario'],
        
        'listEventi' => ['Operatore','Dirigente','Funzionario','Volontario','VF'],
        'listEventiChiusi' => ['Operatore','Dirigente','Funzionario'],
        'listEventiArchiviati' => ['Operatore','Dirigente','Funzionario'],
        'viewEvento' => ['Operatore','Dirigente','Funzionario','Volontario','VF'],
        
        'createEvento'=> ['Dirigente'],
        'publicEvento' => ['Operatore','Dirigente','Funzionario'],
        'assignEvento' => ['Operatore','Dirigente','Funzionario'],
        'manageEvento'=> ['Operatore','Dirigente','Funzionario'],
        'updateEvento'=> ['Operatore','Dirigente','Funzionario'],
        'closeEvento' => ['Operatore','Dirigente','Funzionario'],
        'openClosedEvento' => ['Dirigente','Funzionario'],

        'createRichiestaDos' => ['Operatore','Dirigente','Funzionario'],
        'updateRichiestaDos' => ['Operatore','Dirigente','Funzionario','VF'],

        'createRichiestaCanadair' => ['Operatore','Dirigente','Funzionario'],
        'updateRichiestaCanadair' => ['Operatore','Dirigente','Funzionario','VF'],

        'createSchedaCoc' => ['Operatore','Dirigente','Funzionario'],
        'updateSchedaCoc' => ['Operatore','Dirigente','Funzionario'],

        'createRichiestaElicottero' => ['Operatore','Dirigente','Funzionario','VF'],
        'updatePartialRichiestaElicottero' => ['Funzionario', 'Operatore'],
        'updateRichiestaElicottero' => ['Dirigente'],
        'sendRichiestaElicotteroToCOAU' => ['Operatore','Dirigente','Funzionario'],

        'exportData' => ['Operatore','Dirigente','Funzionario'],

        'viewOrganizzazione' => ['Operatore','Dirigente','Funzionario','Volontario','VF'],
        'viewSede' => ['Operatore','Dirigente','Funzionario','Volontario','VF'],
        'viewAutomezzo' => ['Operatore','Dirigente','Funzionario','Volontario','VF'],
        'viewAttrezzatura' => ['Operatore','Dirigente','Funzionario','Volontario','VF'],

        'listAllerte'   => ['Dirigente','Funzionario'],
        'createAllerta' => ['Dirigente','Funzionario'],
        'updateAllerta' => ['Dirigente', 'Funzionario'],
        'deleteAllerta' => ['Dirigente', 'Funzionario'],

        'listMasTemplate'   => ['Dirigente','Funzionario'],
        'createMasTemplate' => ['Dirigente','Funzionario'],
        'updateMasTemplate' => ['Dirigente', 'Funzionario'],
        'deleteMasTemplate' => ['Dirigente', 'Funzionario'],

        'listMasMessage'   => ['Dirigente','Funzionario'],
        'createMasMessage' => ['Dirigente','Funzionario'],
        'updateMasMessage' => ['Dirigente', 'Funzionario'],
        'deleteMasMessage' => ['Dirigente', 'Funzionario'],
        'sendMasMessage' => ['Dirigente', 'Funzionario'],

        'listMasRubrica'   => ['Dirigente','Funzionario'],
        'createMasRubrica' => ['Dirigente','Funzionario'],
        'updateMasRubrica' => ['Dirigente', 'Funzionario'],
        'deleteMasRubrica' => ['Dirigente', 'Funzionario'],

        'listRubricaGroup' => ['Dirigente', 'Funzionario'],
        'createRubricaGroup' => ['Dirigente', 'Funzionario'],
        'updateRubricaGroup' => ['Dirigente', 'Funzionario'],
        'deleteRubricaGroup' => ['Dirigente', 'Funzionario'],

        'listAppUser' => ['Dirigente', 'Funzionario'],
        'createAppUser' => ['Dirigente', 'Funzionario'],
        'updateAppUser' => ['Dirigente', 'Funzionario'],
        'deleteAppUser' => ['Dirigente', 'Funzionario'],

        'ManageEverbridge' => ['Admin'],

        'viewTipoRisorsaMeta' => ['Admin'],
        'updateTipoRisorsaMeta' => ['Admin'],
        'deleteTipoRisorsaMeta' => ['Admin'],

        'adminPermissions' => ['Admin']
    ];

    /**
     * Alberatura permessi con permessi figli
     * @var [type]
     */
    private $permission_children_map = [
        'adminPermissions' => [
            'createSegnalazione',
            'viewSegnalazione',
            'listSegnalazioni',
            'listEventiChiusi',
            'listEventiArchiviati',
            'updateSegnalazione',
            'closeSegnalazione',            
            'listEventi',
            'viewEvento',
            'transformSegnalazioneToEvento',
            'createEvento',
            'publicEvento',
            'assignEvento',
            'manageEvento',
            'updateEvento',
            'closeEvento',
            'openClosedEvento',
            'createRichiestaDos',
            'updateRichiestaDos',
            'createRichiestaCanadair',
            'updateRichiestaCanadair',
            'createRichiestaElicottero',
            'updateRichiestaElicottero',
            'sendRichiestaElicotteroToCOAU',
            'exportData',
            'removeEvento',
            'removeSegnalazione',
            'createSchedaCoc',
            'updateSchedaCoc',

            'viewLog',

            'manageOrganizzazioni',
            'manageSedi',
            'manageOperatori',
            'manageAttrezzature',
            'manageAutomezzi',
            'manageCategorie',
            'manageAggregatori',
            'manageTipiAutomezzo',
            'manageTipiAttrezzatura',
            'manageTipiEvento',
            'manageTipiOrganizzazione',
            'manageSpecializzazioni',

            'manageAllerta',
            'manageAppUser',
            'manageMasTemplate',
            'manageMasMessage',
            'manageMasRubrica',
            'manageRubricaGroup',
            'manageVolontari',

            'manageRbac'
        ],
        'manageAllerta' => [
            'listAllerte',
            'createAllerta',
            'updateAllerta',
            'deleteAllerta'
        ],
        'manageAppUser' => [
            'listAppUser',
            'createAppUser',
            'updateAppUser',
            'deleteAppUser'
        ],
        'manageMasTemplate' => [
            'listMasTemplate',
            'createMasTemplate',
            'updateMasTemplate',
            'deleteMasTemplate'
        ],
        'manageMasMessage' => [
            'listMasMessage',
            'createMasMessage',
            'updateMasMessage',
            'deleteMasMessage',
            'sendMasMessage'
        ],
        'manageRubricaGroup' => [
            'listRubricaGroup',
            'createRubricaGroup',
            'updateRubricaGroup',
            'deleteRubricaGroup'
        ],
        'manageMasRubrica' => [
            'listMasRubrica',
            'createMasRubrica',
            'updateMasRubrica',
            'deleteMasRubrica'
        ],
        'manageEvento' => [
            'createTaskEvento',
            'updateTaskEvento',
            'createIngaggio',
            'updateIngaggio'
        ],
        'viewLog' => [
            'viewLogSegnalazioni',
            'viewLogEventi',
            'viewLogIngaggi',
            'viewLogRichiesteElicottero',
            'viewLogRichiesteDos',
            'viewLogRichiesteCanadair'
        ],
        'manageAttrezzature' => [
            'viewAttrezzatura',
            'createAttrezzatura',
            'updateAttrezzatura',
            'deleteAttrezzatura'
        ],
        'manageAutomezzi' => [
            'viewAutomezzo',
            'createAutomezzo',
            'updateAutomezzo',
            'deleteAutomezzo'
        ],
        'manageCategorie' => [
            'viewCategoria',
            'createCategoria',
            'updateCategoria',
            'deleteCategoria'
        ],
        'manageAggregatori' => [
            'viewAggregatore',
            'createAggregatore',
            'updateAggregatore',
            'deleteAggregatore'
        ],
        'manageTipiAutomezzo' => [
            'viewTipoAutomezzo',
            'createTipoAutomezzo',
            'updateTipoAutomezzo',
            'deleteTipoAutomezzo'
        ],
        'manageTipiAttrezzatura' => [
            'viewTipoAttrezzatura',
            'createTipoAttrezzatura',
            'updateTipoAttrezzatura',
            'deleteTipoAttrezzatura'
        ],
        'manageTipiEvento' => [
            'viewTipoEvento',
            'createTipoEvento',
            'updateTipoEvento',
            'deleteTipoEvento',
            'addIconTipoEvento'
        ],
        'manageOperatori' => [
            'viewOperatore',
            'createOperatore',
            'updateOperatore',
            'deleteOperatore'
        ],
        'manageOrganizzazioni' => [
            'viewOrganizzazione',
            'createOrganizzazione',
            'updateOrganizzazione',
            'deleteOrganizzazione'
        ],
        'manageTipiOrganizzazione' => [
            'viewTipoOrganizzazione',
            'createTipoOrganizzazione',
            'updateTipoOrganizzazione',
            'deleteTipoOrganizzazione'
        ],
        'manageSpecializzazioni' => [
            'viewSpecializzazione',
            'createSpecializzazione',
            'updateSpecializzazione',
            'deleteSpecializzazione'
        ],
        'manageSedi' => [
            'viewSede',
            'createSede',
            'updateSede',
            'deleteSede'
        ],
        'manageVolontari' => [
            'viewVolontario',
            'createVolontario',
            'updateVolontario',
            'deleteVolontario'
        ],
    ];


    /**
     * Aggiorna i permessi dell'applicativo
     * ./yii rbac/update-permissions
     * @return [type] [description]
     */
    public function actionUpdatePermissions( $mantain = 0 )
    {
        $auth = Yii::$app->authManager;

        $map = $this->permissions_map;

        $all_roles = $auth->getRoles();
        /**
         * Per evitare che rimangano refusi successivamente a aggiornamento
         * Rimuovo i permessi da ogni ruolo,
         * questo significa che si perdono le associazioni tra permessi figli e genitori, che probabilmente non è necessario utilizzare
         */
        if($mantain == 0) {
            foreach ($all_roles as $single_role) :
                $auth->removeChildren($single_role);
            endforeach;
        }

        $roles = [];
        foreach ($map as $key => $value) :
            if (!$permission = $auth->getPermission( $key )) :
                echo "Aggiungo permesso " . $key . "\n";
                $permission = $auth->createPermission( $key );
                $auth->add($permission);
            endif;

            foreach ($value as $rl) :

                $roles[] = $rl;

                if(!$role = $auth->getRole($rl)) :
                    echo "Aggiungo ruolo " . $rl . "\n";
                    $role = $auth->createRole($rl);
                    $auth->add($role);
                endif;

                if(!$auth->hasChild($role, $permission)) :
                    echo "Aggiungo permesso " . $key . " a ruolo " . $rl . "\n";
                    $auth->addChild($role, $permission);
                endif;
            endforeach;
        endforeach;


        /**
         * Cancello tutti i ruoli non presenti nella nuova mappatura
         */
        if( $mantain == 0 ) :
            foreach ($all_roles as $single_role) :
                if( !in_array( $single_role->name, $roles ) ) :
                    $auth->remove($single_role);
                endif;
            endforeach;
        endif;


        $perm_map = $this->permission_children_map;
        foreach ($perm_map as $key => $value) :
            if (!$permission = $auth->getPermission( $key )) :
                echo "Aggiungo permesso " . $key . "\n";
                $permission = $auth->createPermission( $key );
                $auth->add($permission);
            endif;

            if( $mantain == 0 ) $auth->removeChildren($permission);

            foreach ($value as $pr) :

                if (!$temp_permission = $auth->getPermission( $pr )) :
                    echo "Aggiungo permesso " . $pr . "\n";
                    $temp_permission = $auth->createPermission( $pr );
                    $auth->add($temp_permission);
                endif;
                
                if(!$auth->hasChild($permission, $temp_permission)) :
                    echo "Aggiungo permesso " . $pr . " a permesso " . $key . "\n";
                    $auth->addChild($permission, $temp_permission);
                endif;

            endforeach;
        endforeach;

        


    }


}