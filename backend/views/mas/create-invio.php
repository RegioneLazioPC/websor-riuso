<?php

use yii\helpers\Html;
use common\models\AlmZonaAllerta;

/* @var $this yii\web\View */
/* @var $model common\models\MasMessageTemplate */

$this->title = 'Invia messaggio';
$this->params['breadcrumbs'][] = ['label' => 'Messaggi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="mass-message-template-create" ng-app="AppRubrica" ng-controller="ResendMessageController">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-sm-12">
            <?php 
             
            echo $this->render('_partial_header_messaggio', [
                'model'=>$model
            ]);

            $channels = [
                'channel_mail' => [
                    'presente' => $model->channel_mail ? 1 : 0,
                    'label' => 'Email'
                ],
                'channel_fax' => [
                    'presente' => $model->channel_fax ? 1 : 0,
                    'label' => 'Fax'
                ],
                'channel_pec' => [
                    'presente' => $model->channel_pec ? 1 : 0,
                    'label' => 'Pec'
                ],
                'channel_sms' => [
                    'presente' => $model->channel_sms ? 1 : 0,
                    'label' => 'Sms'
                ],
                'channel_push' => [
                    'presente' => $model->channel_push ? 1 : 0,
                    'label' => 'Push'
                ]
            ];

            foreach ($channels as $key => $ch) {
                
                ?>
                
                <label style="margin-right: 10px;">
                    <input type="checkbox"
                        disabled 
                        value="" <?php if($ch['presente'] == 1) echo "checked";?> />
                        <?php echo $ch['label'];?>
                </label>
                
                <?php
            }
            ?>    

            <?php 
            if(@$model->allerta)    {
                ?>

                <br />
                <h3>Zone di allerta</h3>
                
                <?php 
                $zone = explode(",", $model->allerta->zone_allerta);
                foreach (AlmZonaAllerta::find()->orderBy(['code'=>SORT_ASC])->all() as $zona) {
                    ?>
                    <div class="col-md-1">
                        <input type="checkbox"
                            disabled 
                            value="" 
                            <?php if(in_array($zona->code, $zone)) echo "checked";?> />
                            <?php echo Html::encode($zona->code);?>
                    </div>
                    <?php
                }
                ?>
            <?php 
                }
            ?>
        </div>
    </div>

    <hr />
    
   <div class="row">
        <div class="col-lg-12">
           <div ng-controller="RubricaController as $rubrica_ctrl">
                <div ui-i18n="{{lang}}">
                    <div style="margin-bottom: 10px" class="btn-group" role="group" aria-label="Lista">
                      <button ng-class="{'btn-info': current_view == 'contatti'}" type="button" ng-click="setView('contatti')" class="btn btn-secondary">Contatti</button>
                      <button ng-class="{'btn-info': current_view == 'gruppi'}" type="button" ng-click="setView('gruppi')" class="btn btn-secondary">Gruppi</button>
                    </div>
                    <div ng-show="current_view == 'contatti'">
                        <input style="margin: 10px 5px 10px 0;" type="button" ng-click="exportRubrica()" class="btn btn-success btn-sm" value="esporta csv" />
                        <div id="ui_grid1" ui-grid="uiContactsGrid" class="grid" ui-grid-resize-columns ui-grid-move-columns ui-grid-selection ui-grid-exporter></div>
                    </div>
                    <div ng-show="current_view == 'gruppi'">
                        <input style="margin: 10px 5px 10px 0;" type="button" ng-click="exportGruppi()" class="btn btn-success btn-sm" value="esporta csv" />
                        <div id="ui_grid2" ui-grid="uiGroupsGrid" class="grid" ui-grid-resize-columns ui-grid-move-columns ui-grid-selection ui-grid-exporter></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div ng-init="init(<?php echo $model->id;?>,'<?php echo Yii::$app->request->csrfToken;?>')"></div>

    <div ng-class="{'show': block_form, 'hidden': !block_form}" 
    style="display:none; width: 100vw;height: 100vh;position: fixed;top: 0;left: 0;background-color: rgba(0,0,0,.4);z-index: 9991;">
        <em class="fa fa-spinner fa-spin" style="margin-left: -15px; margin-top: -15px;color: #fff; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); font-size: 30px;"></em>
        <p style="position: absolute; top: 50%; margin-top: 25px; width: 100vw;text-align: center; color: #fff">
            {{spin_message}} <br />
            <span style="display: block" ng-repeat="error in errors" class="text-danger">
              {{error}}
            </span>
            <input ng-if="can_reset" style="margin-top: 20px;" type="button" ng-click="resetAll()" class="btn btn-danger btn-sm" value="Resetta tutto" />
        </p>

    </div>

    <button style="margin-top: 10px;" ng-class="{'disabled':block_form}" type="button" ng-click="resend()" class="btn btn-success">Reinvia</button>

    <div class="row">
        <div class="col-lg-12">
        <div style="margin-top: 30px;">
            <?php 
            echo $this->render('_partial_anteprima', [
                'model'=>$model
            ]);
            ?>
            <hr />
            <h3>Note</h3>
            <?= Html::encode($model->note); ?>
        </div>
    </div>
</div>
