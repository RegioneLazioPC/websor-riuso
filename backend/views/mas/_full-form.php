<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\FileInput;

use dosamigos\tinymce\TinyMce;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Tabs;

use common\models\MasMessage;
use common\models\MasMessageTemplate;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\AlmAllertaMeteo */
/* @var $form yii\widgets\ActiveForm */
$model->title = "";


$model->channel_fax = false;
$model->channel_mail = true;
$model->channel_sms = true;
$model->channel_push = true;
$model->channel_pec = true;
?>
<div ng-app="AppRubrica" ng-controller="CreaMessaggioController as ctrl">
	
	<?php $form = ActiveForm::begin([
		'options'=>[
			'name' => 'sendAllertaForm',
			'ng-submit' => 'sendForm.submit(sendAllertaForm, $event)',			
		]
	]); ?>
	<div class="alm-allerta-meteo-form">

	    <?= $form->errorSummary($model); ?>

	    <div class="row">
	    	<div class="col-md-6">
	    		<div>
			        <?= $form->field($model, 'title')->textInput([
			                'label'=>'Titolo del model',
			                'ng-model' => 'ctrl.title',
			                'ng-init' => "ctrl.title = '".$model->title."'"
			            ]) ?>
			    </div>

			    <?php 
			    echo $form
				    ->field($model, 'mediaFile[]',[])
				    ->widget(FileInput::classname(), ['options'=>[
				    	'multiple' => true, 
				    	'accept'=>implode(", ", MasMessage::validMessageMimes())/*"application/pdf"*/, 'ng-model'=>'ctrl.mediaFile',
				    	'onchange'=>"angular.element(this).scope().fileNameChanged(this)"
				]])
				    ->label('Inserisci documento'); 
			    ?>	
			   
			    <?php 
		            echo $form->field($model, 'id_template', ['options' => ['class'=>'']])->widget(Select2::classname(), [
		                'data' => ArrayHelper::map( MasMessageTemplate::find()->all(), 'id', 'nome'),
		                'options' => [
		                    'placeholder' => 'Template...',
		                    'ng-model' => 'ctrl.template',
		                    'ng-disabled' => 'ctrl.tp',
		                    'ng-change' => 'changedTamplate()',
		                    'ng-init' => "ctrl.template = '".$model->id_template."'"
		                ],
		                'pluginOptions' => [
		                    'allowClear' => true
		                ],
		            ])->label('Seleziona template');

		        ?>
		        <h3>Canale</h3>
	    		<p>Seleziona i canali da utilizzare</p>
	    		<div class="row">
			        <div class="col-md-2">
			            <?= $form->field($model, 'channel_mail')->checkbox([
			                'label'=>'Email',
			                'ng-model' => 'ctrl.channel_mail',
			                'ng-true-value' => 1,
			                'ng-init' => "ctrl.channel_mail = ".$model->channel_mail
			            ]) ?>
			        </div>
			        <div class="col-md-2">
			        <?= $form->field($model, 'channel_pec')->checkbox([
			            'label'=>'Pec',
			                'ng-model' => 'ctrl.channel_pec',
			                'ng-true-value' => 1,
			                'ng-init' => "ctrl.channel_pec = ".$model->channel_pec
			        ]) ?>
			        </div>
			        <div class="col-md-2">
			        <?= $form->field($model, 'channel_fax')->checkbox([
			            'label'=>'Fax',
			                'ng-model' => 'ctrl.channel_fax',
			                'ng-true-value' => 1,
			                'ng-init' => "ctrl.channel_fax = ".$model->channel_fax
			        ]) ?>
			        </div>
			        <div class="col-md-2">
			        <?= $form->field($model, 'channel_sms')->checkbox([
			            'label'=>'Sms',
			                'ng-model' => 'ctrl.channel_sms',
			                'ng-true-value' => 1,
			                'ng-init' => "ctrl.channel_sms = ".$model->channel_sms
			        ]) ?>
			        </div>
			        <div class="col-md-3">
				        <?= $form->field($model, 'channel_push')->checkbox([
				            'label'=>'Push notification',
			                'ng-model' => 'ctrl.channel_push',
			                'ng-true-value' => 1,
			                'ng-init' => "ctrl.channel_push = ".$model->channel_push
				        ]) ?>
			        </div>
			   	</div>
	    	</div>
	    	<div class="col-md-6">
	    		
		        <?= $form->field($model, 'note')->textarea(['maxlength' => true, 'style'=>"height: 200px"]) ?>
		    
	    	</div>
	    </div>
	    
	</div>
	<div class="mass-message-form" ng-controller="TemplatePreviewController as tmplCtrl">

	    <div class="row">
	    	<div class="col-md-6">
	    		<h2>Messaggio</h2>
			    <p>Inserisci il model, se hai selezionato un template il model inserito qui andrà a sostituire la dicitura <strong>{{message}}</strong> del template</p>


			    <?php 
			        echo Tabs::widget([
			        'linkOptions'=>[
			        	'ng-click'=>'setChannel($event)',
			        ],
			        'items' => [
			            [
			                'label' => 'Mail',
			                'content' => $form->field($model, 'mail_text')->textarea(
			                	[
			                		'rows' => 6,'maxlength' => true,
			                		'ng-model' => 'tmplCtrl.mail_text',
			                		'ng-change' => 'updatePreview()',
			                		'ng-init' => 'tmplCtrl.mail_text = "' . $model->mail_text . '"'
			                ])->label('Testo mail e pec'),
			                'options' => ['id' => 'mail_text'],
			            ],
			            [
			                'label' => 'Fax',
			                'content' => $form->field($model, 'fax_text')->textarea([
			                	'rows' => 6,
			                	'maxlength' => true,
			                	'ng-model' => 'tmplCtrl.fax_text',
			                	'ng-change' => 'updatePreview()',
			                	'ng-init' => 'tmplCtrl.fax_text = "' . $model->fax_text . '"'
			                ])->label('Testo fax'),
			                'options' => ['id' => 'fax_text', 'ng-click'=>'setChannel("fax_text")'],
			            ],
			            [
			                'label' => 'Sms',
			                'content' => $form->field($model, 'sms_text')->textarea([
			                	'rows' => 6,
			                	'maxlength' => true,
			                	'ng-model' => 'tmplCtrl.sms_text',
			                	'ng-change' => 'updatePreview()',
			                	'ng-init' => 'tmplCtrl.sms_text = "' . $model->sms_text . '"'
			                ])->label('Testo sms'),
			                'options' => ['id' => 'sms_text', 'ng-click'=>'setChannel("sms_text")'],
			            ],
			            [
			                'label' => 'Push notifications',
			                'content' => $form->field($model, 'push_text')->textarea([
			                	'rows' => 6,
			                	'maxlength' => true,
			                	'ng-model' => 'tmplCtrl.push_text',
			                	'ng-change' => 'updatePreview()',
			                	'ng-init' => 'tmplCtrl.push_text = "' . $model->push_text . '"'
			                ])->label('Testo notifiche push'),
			                'options' => ['id' => 'push_text', 'ng-click'=>'setChannel("push_text")'],
			            ],
			        ],
			    ]);
			    ?>
	    	</div>
	    	<div class="col-md-6">
	    		<h2>Anteprima</h2>
	    		<button style="margin-bottom: 20px;" type="button" class="btn btn-warning btn-sm" ng-click="updatePreview()">
	    			Aggiorna anteprima
	    		</button>
	    		<h3>{{replaceChannelName()}}</h3>
	    		<div ng-bind-html="preview">
	    		</div>
	    	</div>
	    </div>
	    

	    
	    
	</div>


    <div ng-controller="RubricaController as $rubrica_ctrl">
		<div ui-i18n="{{lang}}">
			<div class="row">
				<div class="col-lg-12">
	        		<div >
	        			<h4>Seleziona gruppi</h4>
		            	<input style="margin: 10px 5px 10px 0;" type="button" ng-click="exportGruppi()" class="btn btn-success btn-sm" value="esporta gruppi csv" />
		            	<div id="ui_grid2" ui-grid="uiGroupsGrid" class="grid" ui-grid-resize-columns ui-grid-move-columns ui-grid-selection ui-grid-exporter></div>
					</div>
	        	</div>
			</div>
			<div class="row">
	        	<div class="col-lg-12">
	        		<div >
	        			<h4>Seleziona singoli contatti</h4>
		            	<input style="margin: 10px 5px 10px 0;" type="button" ng-click="exportRubrica()" class="btn btn-success btn-sm" value="esporta contatti csv" />
		            	<div id="ui_grid1" ui-grid="uiContactsGrid" class="grid" ui-grid-resize-columns ui-grid-move-columns ui-grid-selection ui-grid-exporter></div>
					</div>
	        	</div>
	        </div>
			
		</div>
	</div>
	
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
 
	<div class="form-group">
		<?= Html::hiddenInput('check', '', ['id'=>'add_checks', 'ng-value' => 'contacts'])?>
		<?= Html::hiddenInput('group_check', '', ['id'=>'add_group_checks', 'ng-value' => 'groups'])?>
		<input style="margin-top: 20px;" type="submit" class="btn btn-info btn-lg btn-block" value="Conferma e invia il messaggio" />
    </div>
<?php ActiveForm::end(); ?>
    
</div>

<?php 
Modal::begin([
    'header' => '<h2>Anteprima</h2>',
    'id' => 'modal'        
]);

?><div id="modal_content"></div><?php

Modal::end();


