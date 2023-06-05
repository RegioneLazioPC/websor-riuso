<?php 
use kartik\widgets\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

use common\models\UtlAnagrafica;
use common\models\LocComune;
use common\models\UserChangePassword;

$change_pwd_model = new UserChangePassword;
$comuni = LocComune::find()->where([
                                Yii::$app->params['region_filter_operator'], 
                                'id_regione', 
                                Yii::$app->params['region_filter_id']
                            ])->all();

$is_operatore = ($model->operatore) ? true : false;
$anagrafica = (!empty($model->utente)) ? $model->utente->anagrafica : ($is_operatore ? $model->operatore->anagrafica : null);


?>

<div class="utl-utente-form">

    <?php 
    if(!empty($anagrafica)) {
    
	    $form = ActiveForm::begin([
	        
	    ]); ?>

	    
	    <div class="row p20w p20h">
	    	
		    	<div class="p20h col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-grayLighter box_shadow">

		    		<?php 
		    		if(!empty($ana_errors)) :	    			
		    			foreach ($ana_errors as $error) {
		    				foreach ($error as $error_message) {
		    				 	echo '<p class="text-danger">'.Html::encode($error_message).'</p>';
		    				 } 
		    			}
		    		endif;
		    		?>

		            <?= $form->field($anagrafica, 'nome', ['options' => ['class' => 'col-md-3 no-pl']])->textInput(['maxlength' => true]) ?>

		            <?= $form->field($anagrafica, 'cognome', ['options' => ['class' => 'col-md-3 no-pl']])->textInput(['maxlength' => true]) ?>

		            <?= $form->field($anagrafica, 'telefono', ['options' => ['class' => 'col-md-3 no-pl']])->textInput(['maxlength' => true, 'value' => @$anagrafica->telefono]) ?>

		            <?= $form->field($anagrafica, 'email', ['options' => ['class' => 'col-md-3 no-pl']])->textInput(['maxlength' => true]) ?>


		            <?= $form->field($anagrafica, 'codfiscale', ['options' => ['class' => 'col-md-3 no-pl']])->textInput(['maxlength' => true]) ?>


		            <?= 
		                $form->field($anagrafica, 'comune_residenza', ['options' => ['class'=>'col-md-3 no-pl']])->widget(Select2::classname(), [
		                    'data' => ArrayHelper::map( $comuni, 'id', 'comune'),
		                    'attribute' => 'org_id',
		                    'options' => [
		                        'multiple' => false,
		                        'theme' => 'krajee',
		                        'placeholder' => 'Cerca comune',
		                        'language' => 'it-IT',
		                        'width' => '100%',
		                    ],
		                    'pluginOptions' => [
		                        'allowClear' => true
		                    ],
		                ]);

		            ?>

		            
		            <?php 
		            	$anagrafica->data_nascita = ($anagrafica->data_nascita) ? 
		            		Yii::$app->formatter->asDate($anagrafica->data_nascita) : 
		            		"";
		            ?>
		            <?php echo $form->field($anagrafica, 'data_nascita', ['options' => ['class' => 'col-md-3 no-pl']])->widget(DatePicker::classname(), [
		                    'options' => ['placeholder' => 'Data di nascita ...'],
		                    'pluginOptions' => [
		                        'autoclose'=>true,
		                        'language' => 'it',
		                        'format' => 'dd-mm-yyyy'
		                    ],
		                ]); ?>

		            <?= 
		                $form->field($anagrafica, 'luogo_nascita', ['options' => ['class'=>'col-md-3 no-pl']])->widget(Select2::classname(), [
		                    'data' => ArrayHelper::map( LocComune::find()->all(), 'id', 'comune'),
		                    'attribute' => 'org_id',
		                    'options' => [
		                        'multiple' => false,
		                        'theme' => 'krajee',
		                        'placeholder' => 'Cerca comune',
		                        'language' => 'it-IT',
		                        'width' => '100%',
		                    ],
		                    'pluginOptions' => [
		                        'allowClear' => true
		                    ],
		                ]);

		            ?>

		    	</div>
		    
	    </div>

	    <div class="form-group m20w ">
	        <?= Html::submitButton('Aggiorna', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>

	    <?php ActiveForm::end(); 
	}
    ?>


    <?php 
    	$form = ActiveForm::begin([
        
    	]); 
    ?>

    <div class="row p20w p20h">
    	
	    	<div class="p20h col-xs-4 col-sm-4 col-md-4 col-lg-4 bg-grayLighter box_shadow">

	    		<?php 
	    		if(!empty($pwd_errors)) :	    			
	    			foreach ($pwd_errors as $error) {
	    				foreach ($error as $error_message) {
	    				 	echo '<p class="text-danger">'.Html::encode($error_message).'</p>';
	    				 } 
	    			}
	    		endif;

	    		if($ok_pwd) echo '<p class="text-success">Password modificata</p>';
	    		?>

	    		<?= $form->field($change_pwd_model, 'old_password', ['options' => ['class' => 'col-md-12 no-pl']])->passwordInput(['maxlength' => false]) ?>

	            <?= $form->field($change_pwd_model, 'new_password', ['options' => ['class' => 'col-md-12 no-pl']])->passwordInput(['maxlength' => false]) ?>

	            <?= $form->field($change_pwd_model, 'repeat_password', ['options' => ['class' => 'col-md-12 no-pl']])->passwordInput(['maxlength' => false]) ?>

	            
	    	</div>
	    
    </div>

    <div class="form-group m20w ">
        <?= Html::submitButton('Modifica password', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>