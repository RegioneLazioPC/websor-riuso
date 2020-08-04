<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;

use common\utils\MasMessageManager;
/* @var $this yii\web\View */
/* @var $model common\models\MasMessage */

$this->title = "Messaggio #" .$model->id . " creato il " . Yii::$app->formatter->asDate($model->created_at);
$this->params['breadcrumbs'][] = ['label' => 'Messaggi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="mass-message-view">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <p>
        <?php echo (Yii::$app->user->can('deleteMasMessage')) ? Html::a('Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-sm',
            'data' => [
                'confirm' => 'Sicuro di voler eliminare questo elemento?',
                'method' => 'post',
            ],
        ]) : "" ?>
        <?php echo (Yii::$app->user->can('sendMasMessage')) ? Html::a('Aggiungi destinatari', ['create-invio', 'id_messaggio' => $model->id], [
            'class' => 'btn btn-primary btn-sm'
        ]) : "" ?>
    </p>

    <hr />
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
                'channel_pec' => [
                    'presente' => $model->channel_pec ? 1 : 0,
                    'label' => 'Pec'
                ],
                'channel_fax' => [
                    'presente' => $model->channel_fax ? 1 : 0,
                    'label' => 'Fax'
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
            ?>
            <div ng-app="AppRubrica" ng-controller="MessageUpdateController as $ctrl">
                <span ng-init="setDef(<?php echo $model->id;?>,'<?php echo Yii::$app->request->csrfToken;?>')"></span>
                <?php 
                
                $init = [];
                $can_edit = Yii::$app->user->can('updateMasMessage');
                foreach ($channels as $key => $ch) {
                    $init[] = ($ch['presente'] == 1) ? 1 : 0;
                    
                    ?>
                    
                    <label style="margin-right: 10px;">
                        <input type="checkbox"
                            <?php if(!$can_edit) echo 'disabled'; ?>  
                            ng-model="channels.<?php echo $key;?>" ng-change="logChannels()"
                            value="" />
                            <?php echo $ch['label'];?>
                    </label>
                    
                    <?php
                }
                
                
                ?>
                
                <div ng-init="initController([<?php echo implode(",", $init);?>])"></div>
                <?php if($can_edit) { ?>
                <div ng-if="changed">
                    <button ng-class="{'disabled':calling}" type="button" ng-click="updateMessage()" class="btn btn-default btn-xs">Aggiorna i canali</button>
                </div>
                <?php } ?>
            </div>

            <div style="margin-top: 30px;">
                <?php 
                echo $this->render('_partial_anteprima', [
                    'model'=>$model
                ]);
                ?>
                <hr />
                <h3>Note</h3>
                <?php echo Html::encode($model->note); ?>
            </div>
        </div>

    </div>

    <?php 
    echo $this->render('_partial-invii', [
        '_model'=>$model
    ]);
    ?>


</div>

