<?php

use yii\helpers\Html;
use kartik\grid\GridView;

use common\models\ConMasInvioContact;
use kartik\export\ExportMenu;

if(!empty($model->mas_ref_id)) {
    $mas_url = '';

    $token = Yii::$app->jwt->getBuilder()
        ->setIssuer(Yii::$app->params['iss']) 
        ->setAudience(Yii::$app->params['aud']) 
        ->setId(Yii::$app->params['tid'], true)
        ->setIssuedAt(time()) 
        ->setNotBefore(time());

    $token->set('stringa_assegnazione', 'WEBSORUSER|||'.Yii::$app->user->identity->username);
    $token->set('role', Yii::$app->params['mas_websor_user_role']);
    $token->set('username', Yii::$app->user->identity->username);

    // mettiamo 1 ora
    $token->setExpiration(time() + (3600));
    $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
    $token = (string) $token->sign($signer, Yii::$app->params['secret-key'])->getToken();

    $mas_url = Yii::$app->params['mas_host_public'] . "v1/auth/set-token?t=" . $token . "&msg_id=" . $model->mas_ref_id;
}

?>

<div class="mas-monitoraggio-angular" ng-app="AppRubrica">
<?php 
    if(Yii::$app->user->can('sendMasMessage')) {
        ?>
    <div ng-controller="InvioController as $invio" ng-init="initController(<?php echo $model->id;?>,'<?php echo Yii::$app->request->csrfToken;?>', <?php echo empty($model->mas_ref_id) ? 1 : 2;?>)"  ui-i18n="{{lang}}">

        <?php if(empty($model->mas_ref_id)) { ?>
        <div class="is_running_mas" ng-class="{'running': is_running, 'not_running': !is_running}">
            <span class="running_light"></span>
            <span ng-if="is_running" class="monitor-feed">Componente in lavorazione</span>
            <span ng-if="!is_running" class="monitor-feed">Componente bloccato</span>

            <input ng-class="{'disabled': calling}" style="margin: 10px 5px 10px 0;" type="button" ng-click="isActiveMas()" class="btn btn-default btn-xs" value="verifica" />
        </div>
        <?php } else {
            ?>
            <a style="margin-bottom: 12px;" href="<?php echo $mas_url;?>" target="_blank">Verifica sulla console di monitoraggio</a><br />
            <?php
        } ?>
        
        <div style="margin-bottom: 10px" class="btn-group" role="group" aria-label="Lista">
          <button ng-class="{'btn-info': current_action == 'contacts'}" type="button" ng-click="loadContacts()" class="btn btn-secondary">Contatti</button>
          <button ng-class="{'btn-info': current_action == 'groupedContacts'}" type="button" ng-click="loadGroupedContacts()" class="btn btn-secondary">Contatti raggruppati</button>
          <?php if(empty($model->mas_ref_id)) { ?>
          <button ng-class="{'btn-info': current_action == 'logs'}" type="button" ng-click="loadMasLog()" class="btn btn-secondary">MAS Log</button>
          <button ng-class="{'btn-info': current_action == 'attempt'}" type="button" ng-click="loadMasAttempt()" class="btn btn-secondary">Dettagli MAS</button>
          <button ng-class="{'btn-info': current_action == 'messages'}" type="button" ng-click="loadMasInvioMessages()" class="btn btn-secondary">Processo</button>
            <?php } ?>
        </div>
        
        <?php if(empty($model->mas_ref_id)) { ?>
        <div ng-if="current_action == 'messages'">
            <p>Il dispatching dei messaggi avviene in asincrono in background, per ogni canale il processo viene splittato dividendo i contatti tra tutti i messaggi creati relativi a un invio, interrompere e processare i messaggi manualmente potrebbe comportare esiti non desiderati, si consiglia di utilizzare le funzionalità solo se i consumer non dovessero essere attivi</p>
            <p>
                <button class="btn btn-default btn-xs disabled" type="button">
                    <span class="fa fa-arrow-right"></span>
                </button>
                <span style="margin-left: 5px">Elabora processo manualmente</span>
            </p>
            <p>
                <button class="btn btn-success btn-xs disabled" type="button">
                    <span class="fa fa-paper-plane"></span>
                </button>
                <span style="margin-left: 5px">Rimetti processo in coda</span>
            </p>
            <p>
                <button class="btn btn-danger btn-xs disabled" type="button">
                    <span class="fa fa-ban"></span>
                </button>
                <span style="margin-left: 5px">Interrompi elaborazione del processo in coda</span>
            </p>
            <p>
                <button class="btn btn-info btn-xs disabled" type="button">
                    <span class="fa fa-refresh"></span>
                </button>
                <span style="margin-left: 5px">Rielabora il feedback (disponibile solo al termine del processamento della coda)</span>
            </p>
        </div>
        <?php } ?>

        <?php if(empty($model->mas_ref_id)) { ?>
        <div ng-if="current_action == 'logs'">
            <h4 style="font-size: 12px; text-transform: uppercase;">Canale</h4>
            <input ng-class="{'btn-info': log_channel == 'Email'}" style="margin: 10px 5px 10px 0;" type="button" ng-click="changeChannel('Email')" class="btn btn-default btn-xs" value="Email" />
            <input ng-class="{'btn-info': log_channel == 'Pec'}" style="margin: 10px 5px 10px 0;" type="button" ng-click="changeChannel('Pec')" class="btn btn-default btn-xs" value="Pec" />
            <input ng-class="{'btn-info': log_channel == 'Fax'}" style="margin: 10px 5px 10px 0;" type="button" ng-click="changeChannel('Fax')" class="btn btn-default btn-xs" value="Fax" />
            <input ng-class="{'btn-info': log_channel == 'Sms'}" style="margin: 10px 5px 10px 0;" type="button" ng-click="changeChannel('Sms')" class="btn btn-default btn-xs" value="Sms" />
            <input ng-class="{'btn-info': log_channel == 'Push'}" style="margin: 10px 5px 10px 0;" type="button" ng-click="changeChannel('Push')" class="btn btn-default btn-xs" value="Push" />
        </div>
        <?php } ?>
        
        <div ng-if="current_action == 'contacts'">
            <div style="margin-bottom: 10px" class="btn-group" role="group" aria-label="Reinvio">
                <button ng-class="{'disabled':calling}" type="button" ng-click="resend()" class="btn btn-success btn-secondary">Reinvia a tutti</button>
                <button ng-class="{'disabled':calling}" type="button" ng-click="resendNotSent()" class="btn btn-warning btn-secondary">Reinvia i non recapitati</button>
                <button ng-class="{'disabled':calling}" type="button" ng-click="resendSelected()" class="btn btn-info btn-secondary">Reinvia ai selezionati</button>
            </div>
        </div>

        <div>
            <button style="margin-bottom: 10px;" type="button" ng-click="updateData()" class="btn btn-secondary"><span class="fa fa-refresh"></span> Aggiorna</button>
            <?php 
            // permettiamo solo all'admin di effetuare il reset
            // disabilito per evitare dei reset non voluti sul mas 
            /*
            if(Yii::$app->user->can('Admin') && !empty($model->mas_ref_id)) { ?>
                <button style="margin-bottom: 10px;" type="button" ng-click="updateData(1)" class="btn btn-secondary"><span class="fa fa-refresh"></span> Aggiorna e resetta i feedback passati</button>
                <?php
            }*/?>
        </div>

        <input ng-if="current_action == 'contacts'" 
            style="margin: 10px 5px 10px 0;" type="button" 
            ng-click="exportContacts()" 
            class="btn btn-success btn-sm" value="Esporta contatti" />
        <input ng-if="current_action == 'groupedContacts'" 
            style="margin: 10px 5px 10px 0;" type="button" 
            ng-click="exportGrouped()" 
            class="btn btn-success btn-sm" value="Esporta contatti raggruppati" />

        <div ng-if="current_action != 'contacts' && current_action != 'messages'" id="ui_grid" ui-grid="uiGrid" class="grid" ui-grid-resize-columns ui-grid-move-columns ui-grid-exporter></div>

        <div ng-if="current_action == 'contacts'" id="ui_grid_expandable" ui-grid="uiGridExpandable" class="grid" ui-grid-expandable ui-grid-resize-columns ui-grid-move-columns ui-grid-selection ui-grid-exporter></div>

        <div ng-if="current_action == 'messages'" id="ui_grid_expandable_messages" ui-grid="uiGridExpandable" class="grid" ui-grid-expandable ui-grid-resize-columns ui-grid-move-columns ui-grid-exporter></div>
        
        <script type="text/ng-template" id="checkTemplate.html">
            <i class="fa" style="margin-top: 10px; margin-left: 25px;" ng-class="{'fa-check text-success' : grid.appScope.isYes( col, row ), 'fa-close text-danger' : !grid.appScope.isYes( col, row )}"></i>
        </script>
        
    </div>

    <?php } ?>

    
</div>