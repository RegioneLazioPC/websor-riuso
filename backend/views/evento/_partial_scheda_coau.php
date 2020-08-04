<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use common\models\User;
use yii\helpers\ArrayHelper;

if($is_applicativo) {

	$form = ActiveForm::begin([
            'action' =>['evento/send-riepilogo-coau', ['id_evento'=>$evento->id,'id_richiesta'=>$richiesta->id]],
            'id' => 'send_' . $richiesta->id
        ]); 
	echo Html::submitButton('Conferma invio', ['class' => 'btn btn-default']);

	echo Html::hiddenInput('id_evento', $evento->id);
	echo Html::hiddenInput('id_richiesta', $richiesta->id);

	$richiesta->scenario = \common\models\RichiestaElicottero::SCENARIO_SEND_COAU;

	echo "<div style='height: 1px; width: 100%;'></div>";
}

?>
<div style="max-width: 1000px;">
	<div style="width: 100%">
		<div style="width: 500px; float: left;">
			<?php echo Html::img('@web/images/logo.png', ['width'=>100]); ?><br />
			<p style="font-size: 11px">
				<strong>A</strong>genzia <strong>R</strong>egionale di <strong>P</strong>rotezione <strong>C</rotezione>strong>ivile<br />
				area emergenze e sala operativa di protezione civile
			</p>
		</div>
	</div>
	<div style="clear: both;"></div>
	<div>
		<div style="width: 300px; float: right;">
			<p style="font-family: sans-serif; font-size: 12px; ">Al<br />
				<strong>C</strong>entro <strong>O</strong>perativo <strong>A</strong>ereo <strong>U</ereo>strong>nificato<br />
				<?php echo Yii::$app->params['coauMail'];?>
			</p>

		</div>
		<div style="width: 100%; height: 2px; clear: both;"></div>
	</div>
	<div style="margin: 50px; text-align: center;">
		<h2 style="font-family: sans-serif; font-size: 18px;">COMUNICAZIONE IMPIEGO AEREOMOBILI REGIONALI<br />AIB <?php echo \DateTime::createFromFormat('Y-m-d H:i:s', $richiesta->created_at)->format('Y');?></h2>
	</div>
	<table style="width: 100%;" summary="Tabella">
		<thead>
			<tr>
				<th scope="col" style="border-bottom: 1px solid #d6d6d6; padding-bottom: 4px; text-align: center;">Tipologia</th>
				<th scope="col" style="border-bottom: 1px solid #d6d6d6; padding-bottom: 4px; text-align: center;">Sigla</th>
				<th scope="col" style="border-bottom: 1px solid #d6d6d6; padding-bottom: 4px; text-align: center;">Missione AIB</th>
				<th scope="col" style="border-bottom: 1px solid #d6d6d6; padding-bottom: 4px; text-align: center;">Ora decollo</th>
				<th scope="col" style="border-bottom: 1px solid #d6d6d6; padding-bottom: 4px; text-align: center;">Zona</th>				
			</tr>
		</thead>
		<tbody>
			<?php 

			foreach ($evento->getRichiesteElicottero()
				->andWhere(['id'=>$richiesta->id])
				->all() as $richiesta) {
				?>
				<tr style="padding: 20px 6px;">
					<td style="font-family: sans-serif; padding: 6px; border-bottom: 1px solid #d6d6d6; text-align: center;"><?php echo (!empty($data->codice_elicottero)) ? " - " : @$richiesta->elicottero->modello;?></td>
					<td style="font-family: sans-serif; padding: 6px; border-bottom: 1px solid #d6d6d6; text-align: center;"><?php echo (!empty($data->codice_elicottero)) ? $data->codice_elicottero : @$richiesta->elicottero->targa;?></td>
					<td style="font-family: sans-serif; padding: 6px; border-bottom: 1px solid #d6d6d6; text-align: center;"><?php echo @$richiesta->missione;?></td>
					<td style="font-family: sans-serif; padding: 6px; border-bottom: 1px solid #d6d6d6; text-align: center;"><?php 
						if(!empty($richiesta->dataora_decollo)){
                            $dt = \DateTime::createFromFormat('Y-m-d H:i:s', $richiesta->dataora_decollo);
                            echo $dt->format('H:i');
                        }
					?></td>
					<td style="font-family: sans-serif; border-bottom: 1px solid #d6d6d6; text-align: center;">
						<table style="width: 100%;" role="none">
							<tbody>
								<tr>
									<td style="font-family: sans-serif; padding: 6px; border: 1px solid #d6d6d6; border-top: none; text-align: center;">
										<?php echo  (!empty($richiesta->comune)) ? $richiesta->comune->provincia->sigla : " - " ;?>
									</td>
									<td style="font-family: sans-serif; padding: 6px; border: 1px solid #d6d6d6; border-top: none; text-align: center;"><?php echo (!empty($richiesta->comune)) ? $richiesta->comune->comune : " - " ;?></td>
								</tr>
								<tr>
									<td style="font-family: sans-serif; padding: 6px; text-align: center; border-left: 1px solid #d6d6d6; border-right: 1px solid #d6d6d6;" colspan="2">
										<strong>LOCALIT&Agrave;</strong>strong>
									</td>
								</tr>
								<tr>
									<td style="font-family: sans-serif; padding: 6px; border: 1px solid #d6d6d6; border-bottom: none; text-align: center;" colspan="2">
										<?php echo !empty($richiesta->localita) ? $richiesta->localita : " - ";?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>	
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
	<div style="width: 100%">
		<div style="float: right; display: inline-block; padding: 50px; width: 30%;">
			<div style="width: 80%">
				<h3 style="font-family: sans-serif; font-size: 14px; margin-bottom: 20px;">SOR <?= strtoupper(Yii::$app->params['REGION_NAME']);?></h3>
				<p>
					<?php
					if($is_applicativo && empty($richiesta->id_anagrafica_funzionario)) {

						$users = User::findByRolesName(['Dirigente', 'Funzionario'])->asArray()
						->with(['operatore','operatore.anagrafica'])->all();
						$populate_select = [];
						foreach ($users as $user) {
							$populate_select[ $user['operatore']['anagrafica']['id'] ] = $user['operatore']['anagrafica']['nome'] . " " . $user['operatore']['anagrafica']['cognome'];
						}

		                echo $form->field($richiesta, 'id_anagrafica_funzionario', ['options' => 
		                	['class'=>'col-lg-12 no-pl no-pr']])
		                ->dropDownList(
				            $populate_select,           
				            ['prompt'=>'']    
				        )
				        ->label('Funzionario');
		            
		            } else {

		            	if(!empty($richiesta->id_anagrafica_funzionario)) {
		            		echo $richiesta->funzionario->nome . " " . $richiesta->funzionario->cognome;

		            		echo Html::hiddenInput('RichiestaElicottero[id_anagrafica_funzionario]', $richiesta->id_anagrafica_funzionario);
		            	}

		            }

		            ?>
				</p>
			</div>
		</div>
		<div style="float: left; display: inline-block; padding: 50px; width: 30%;">
			Roma, li <?php echo date('d/m/Y H:i');?>
		</div>
		<div
		style="width: 100%;
    height: 2px;
    clear: both;"
		></div>
	</div>
</div>
<?php 
	if($is_applicativo) ActiveForm::end();
?>