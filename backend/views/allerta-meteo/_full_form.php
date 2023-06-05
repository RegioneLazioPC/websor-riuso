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

use common\models\AlmZonaAllerta;

/* @var $this yii\web\View */
/* @var $model common\models\AlmAllertaMeteo */
/* @var $form yii\widgets\ActiveForm */


$messaggio->title = "Allertamento del sistema di Protezione Civile Regionale del ".date("d-m-Y");
$model->data_allerta = \DateTime::createFromFormat('Y-m-d', date("Y-m-d", time()))->format('d-m-Y');


$messaggio->channel_fax = false;
$messaggio->channel_mail = true;
$messaggio->channel_sms = true;
$messaggio->channel_push = true;
$messaggio->channel_pec = true;
?>
<div ng-app="AppRubrica" ng-controller="CreaAllertaMeteoController as ctrl">
	
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
			        <?= $form->field($messaggio, 'title')->textInput([
			                'label'=>'Titolo del messaggio',
			                'ng-model' => 'ctrl.title',
			                'ng-init' => "ctrl.title = '".$messaggio->title."'"
			            ]) ?>
			    </div>
			    <?php 
			    	echo $form->field($model, 'data_allerta', ['options' => ['class' => '']])->widget(
			    	DatePicker::classname(), [
			            'options' => ['placeholder' => 'Data allerta...',
			            	'ng-model' => 'ctrl.data_allerta',
			                'ng-init' => "ctrl.data_allerta = '".$model->data_allerta."'"
			        	],
			            'pluginOptions' => [
			                'autoclose' => true,
			                'language' => 'it',
			                'format' => 'dd-mm-yyyy'
			            ],
			        ]); 
			    ?>

			    <?php 
			    echo $form
				    ->field($model, 'mediaFile[]',[])
				    ->widget(FileInput::classname(), ['options'=>[
				    	'multiple' => true, 
				    	'accept'=> MasMessage::validAllertaMimes(),
				    	'ng-model'=>'ctrl.mediaFile',
				    	'onchange'=>"angular.element(this).scope().fileNameChanged(this)"
				]])
				    ->label('Inserisci documento'); 
			    ?>	

			    <?= 
		            $form->field($messaggio, 'id_template', ['options' => ['class'=>'']])->widget(Select2::classname(), [
		                'data' => ArrayHelper::map( MasMessageTemplate::find()->all(), 'id', 'nome'),
		                'options' => [
		                    'placeholder' => 'Template...',
		                    'ng-model' => 'ctrl.template',
		                    'ng-disabled' => 'ctrl.tp',
		                    'ng-change' => 'changedTamplate()',
		                    'ng-init' => "ctrl.template = '".$messaggio->id_template."'"
		                ],
		                'pluginOptions' => [
		                    'allowClear' => true
		                ],
		            ])->label('Seleziona template messaggio');

		        ?>

		       	<div class="m10h" ng-controller="TemplatePreviewController as tmplCtrl">
			        <h3>Messaggio</h3>
				    <p>Inserisci il messaggio, se hai selezionato un template il messaggio inserito qui andrà a sostituire la dicitura <strong>{{message}}</strong> del template</p>


				    <?php 
				        echo Tabs::widget([
				        'linkOptions'=>[
				        	'ng-click'=>'setChannel($event)',
				        ],
				        'items' => [
				           
				            [
				                'label' => 'Mail',
				                'content' => $form->field($messaggio, 'mail_text')->textarea(
				                	[
				                		'rows' => 6,'maxlength' => true,
				                		'ng-model' => 'tmplCtrl.mail_text',
				                		'ng-change' => 'updatePreview()',
				                		'ng-init' => 'tmplCtrl.mail_text = "' . $messaggio->mail_text . '"'
				                ])->label('Testo mail e pec'),
				                'options' => ['id' => 'mail_text', 'ng-click'=>'setChannel("mail_text")'],
				            ],
				            [
				                'label' => 'Fax',
				                'content' => $form->field($messaggio, 'fax_text')->textarea([
				                	'rows' => 6,
				                	'maxlength' => true,
				                	'ng-model' => 'tmplCtrl.fax_text',
				                	'ng-change' => 'updatePreview()',
				                	'ng-init' => 'tmplCtrl.fax_text = "' . $messaggio->fax_text . '"'
				                ])->label('Testo fax'),
				                'options' => ['id' => 'fax_text', 'ng-click'=>'setChannel("fax_text")'],
				            ],
				            [
				                'label' => 'Sms',
				                'content' => $form->field($messaggio, 'sms_text')->textarea([
				                	'rows' => 6,
				                	'maxlength' => true,
				                	'ng-model' => 'tmplCtrl.sms_text',
				                	'ng-change' => 'updatePreview()',
				                	'ng-init' => 'tmplCtrl.sms_text = "' . $messaggio->sms_text . '"'
				                ])->label('Testo sms'),
				                'options' => ['id' => 'sms_text', 'ng-click'=>'setChannel("sms_text")'],
				            ],
				            [
				                'label' => 'Push notifications',
				                'content' => $form->field($messaggio, 'push_text')->textarea([
				                	'rows' => 6,
				                	'maxlength' => true,
				                	'ng-model' => 'tmplCtrl.push_text',
				                	'ng-change' => 'updatePreview()',
				                	'ng-init' => 'tmplCtrl.push_text = "' . $messaggio->push_text . '"'
				                ])->label('Testo notifiche push'),
				                'options' => ['id' => 'push_text', 'ng-click'=>'setChannel("push_text")'],
				            ],
				        ],
				    ]);
				    ?>

				    <h2>Anteprima</h2>
		    		<button style="margin-bottom: 20px;" type="button" class="btn btn-warning btn-sm" ng-click="updatePreview()">
		    			Aggiorna anteprima
		    		</button>
		    		<h3>{{replaceChannelName()}}</h3>
		    		<div ng-bind-html="preview">
		    		</div>

	    		</div>

	    	</div>
	    	<div class="col-md-6">
	    		<h3>Canale</h3>
	    		<p>Seleziona i canali da utilizzare</p>
	    		<div class="row">
			        <div class="col-md-2">
			            <?= $form->field($messaggio, 'channel_mail')->checkbox([
			                'label'=>'Email',
			                'ng-model' => 'ctrl.channel_mail',
			                'ng-true-value' => 1,
			                'ng-init' => "ctrl.channel_mail = ".$messaggio->channel_mail
			            ]) ?>
			        </div>
			        <div class="col-md-2">
			        <?= $form->field($messaggio, 'channel_pec')->checkbox([
			            'label'=>'Pec',
			                'ng-model' => 'ctrl.channel_pec',
			                'ng-true-value' => 1,
			                'ng-init' => "ctrl.channel_pec = ".$messaggio->channel_pec
			        ]) ?>
			        </div>
			        <div class="col-md-2">
			        <?= $form->field($messaggio, 'channel_fax')->checkbox([
			            'label'=>'Fax',
			                'ng-model' => 'ctrl.channel_fax',
			                'ng-true-value' => 1,
			                'ng-init' => "ctrl.channel_fax = ".$messaggio->channel_fax
			        ]) ?>
			        </div>
			        <div class="col-md-2">
			        <?= $form->field($messaggio, 'channel_sms')->checkbox([
			            'label'=>'Sms',
			                'ng-model' => 'ctrl.channel_sms',
			                'ng-true-value' => 1,
			                'ng-init' => "ctrl.channel_sms = ".$messaggio->channel_sms
			        ]) ?>
			        </div>
			        <div class="col-md-3">
				        <?= $form->field($messaggio, 'channel_push')->checkbox([
				            'label'=>'Push notification',
			                'ng-model' => 'ctrl.channel_push',
			                'ng-true-value' => 1,
			                'ng-init' => "ctrl.channel_push = ".$messaggio->channel_push
				        ]) ?>
			        </div>
			   	</div>
		        <?= $form->field($messaggio, 'note')->textarea(['maxlength' => true, 'style'=>"height: 50px"]) ?>
				
						    	
		    	<h3>Zone di allerta</h3>
	    		<p>Ad ogni selezione deselezione nella lista contatti verranno selezionati/deselezionati in base alle zone scelte</p>

	    		<?php 
	    		foreach (AlmZonaAllerta::find()->orderBy(['code'=>SORT_ASC])->all() as $zona) {
	    			?>
	    			<div class="col-md-1">
	    			<?php
	    			echo $form->field($model, 'zone_allerta_array['.$zona->code.']')->checkbox([
				            'label'=>$zona->code,
			                'ng-model' => 'ctrl.zona_allerta.'.$zona->code,
			                'ng-true-value' => 1,
			                'ng-init' => "ctrl.zona_allerta.".$zona->code." = 1",
			                'ng-change' => "changedZone('".$zona->code."', ctrl.zona_allerta.".$zona->code.")"
				        ]);
				    ?> 
					</div>
				    <?php
	    		}
	    		?>
	    		<div ng-init="registerEventForContacts()"></div>

	    		<div class="m10h">
					
			       	<?php 
			        $colors = [
			            	'green',
			            	'yellow',
			            	'blue',
			            	'red',
			            	'grey',
			            	'brown',
			            	'orange'
			            ];
			        $zone = AlmZonaAllerta::find()->select(['code',
			            	'ST_AsSVG( st_transform( st_simplify( geom, 500 )  ,4326), 1 ) as path'
			            ])->asArray()->all();
			        ?>
			        <div style="position: absolute; width: 500px; height: 400px;margin-top: 40px;" ></div>
			        <svg width="500" height="400" viewbox="11.2 -43 3 2.3">
			        	<?php 
			        	$n = 0;
			        	foreach ($zone as $z) {
			        		//<?php echo $colors[$n];
			        		?>
			        		<path 
			        			ng-if="ctrl.zona_allerta.<?php echo $z['code'];?> == 1"
			        			id="<?php echo $z['code'];?>"
			        			d="<?php echo $z['path'];?>" 
			        			stroke="none" stroke-width="0" fill="<?php echo $colors[$n];?>" />
			        		<?php
			        		$n++;
			        	}
			        	?>
					</svg>
					<?php  ?>
				</div>
	    	</div>
	    </div>
	    
	</div>
	
	

    <div ng-controller="RubricaController as $rubrica_ctrl" ng-init="setNoSelectionAvaible()">
		<div ui-i18n="{{lang}}">
			
	        
	        <div class="row" style="margin-top: 16px;">
	        	<div class="col-lg-12">
	        		<div >
	        			<h4>Seleziona singoli contatti</h4>
		            	<input style="margin: 10px 5px 10px 0;" type="button" ng-click="exportRubrica()" class="btn btn-success btn-sm" value="esporta contatti csv" />
		            	<div id="ui_grid1" ui-grid="uiContactsGrid" class="grid" ui-grid-resize-columns ui-grid-move-columns ui-grid-selection ui-grid-exporter></div>
					</div>
	        	</div>
	        </div>

	        <div class="row">
				<div class="col-lg-12">
	        		<div >
	        			<h4>Seleziona gruppi</h4>
		            	<input style="margin: 10px 5px 10px 0;" type="button" ng-click="exportGruppi()" class="btn btn-success btn-sm" value="esporta gruppi csv" />
		            	<div id="ui_grid2" ui-grid="uiGroupsGrid" class="grid" ui-grid-resize-columns ui-grid-move-columns ui-grid-selection ui-grid-exporter></div>
					</div>
	        	</div>
			</div>

		</div>
	</div>
	
	<div ng-class="{'show': block_form, 'hidden': !block_form}" 
	style="display:none; width: 100vw;height: 100vh;position: fixed;top: 0;left: 0;background-color: rgba(0,0,0,.4);z-index: 9991;">
        <i class="fa fa-spinner fa-spin" style="margin-left: -15px; margin-top: -15px;color: #fff; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); font-size: 30px;"></i>
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
		<input style="margin-top: 20px;" type="submit" class="btn btn-info btn-lg btn-block" value="Conferma e invia l'allerta" />
		
        
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


