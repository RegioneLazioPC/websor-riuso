<?php 
use yii\helpers\Html;

$today = date('d/m/Y');
?>

<div>
	<div style="width: 100%">
		<div style="width: 500px; float: left;">
			<?php echo Html::img('@web/images/logo.png', ['width'=>100]); ?><br />
			<p style="font-size: 11px">
				<b>A</b>genzia <b>R</b>egionale di <b>P</b>rotezione <b>C</b>ivile<br />
				area emergenze e sala operativa di protezione civile
			</p>
		</div>
	</div>
</div>
<div>
	<h2>DASHBOARD <?= date('d/m/Y (H:i)');?></h2>
	<h3>GENERALE</h3>
</div>
<div>
	<div style="width: 100%; margin-top: 12px;">
		<h4 style="margin-bottom: 12px;">Eventi aperti il <?= $today;?> / tipologia</h4>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<?php echo $images[0] != 'empty' ? Html::img($images[0], ['width'=>300]) : ''; ?>
		</div>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<?php 
			$n = 0;
			foreach ($eventi_attivi_tipologia as $row) {
				?>
				<div style="font-size:11px; margin: 0">
                    <p class="" style="width: 36px; height: 10px; display: inline-block; float: left; background-color: <?= $colors[$n];?>">
                    </p>
                    <p style="float: left; margin-left: 12px; line-height: 12px;">
                    	<b><?= $row['tipologia'];?></b>: <?= $row['conteggio'];?>
                	</p>
                </div>
				<?php
				$n++;
			}
			?>
		</div>
	</div>
	<div style="width: 100%; margin-top: 24px;">
		<h4 style="margin-bottom: 12px;">Eventi aperti il <?= $today;?> / provincia</h4>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<?php echo $images[1] != 'empty' ? Html::img($images[1], ['width'=>300]) : ''; ?>
		</div>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<?php 
			$n = 0;
			foreach ($eventi_attivi_provincia as $row) {
				?>
				<div style="font-size:11px; margin: 0">
                    <p class="" style="width: 36px; height: 10px; display: inline-block; float: left; background-color: <?= $colors[$n];?>">
                    </p>
                    <p style="float: left; margin-left: 12px; line-height: 12px;">
                    	<b><?= $row['sigla'];?></b>: <?= $row['conteggio'];?>
                	</p>
                </div>
				<?php
				$n++;
			}
			?>
		</div>
	</div>
	<div class="break"></div>

	<?php 

    $eventi_aperti_oggi = null;
    $evt_a = null;

    foreach ($eventi_aperti as $e) {
        if($e['ref'] == 'today') {
            $eventi_aperti_oggi = $e;
        } else {
            $evt_a = $e;
        }
    }

    ?>
    
	<div style="width: 100%; margin-top: 24px;">
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<h4>EVENTI APERTI</h4>
			<p class="big_number"><?= $eventi_aperti_oggi['n'];?></p>
			<p style="text-align: center">Eventi aperti il <?= $today;?></p>
			<p class="big_number"><?= $evt_a['n'];?></p>
			<p style="text-align: center">
                Eventi attivi alle <?= date('H:i');?>
            </p>
		</div>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<h4>EVENTI CHIUSI</h4>
			<p class="big_number"><?= $eventi_chiusi[0]['n'];?></p>
			<p style="text-align: center">Eventi chiusi il <?= $today;?></p>
		</div>
	</div>
	

	
</div>
<div class="break"></div>
<div>
	<h3>AIB</h3>
</div>
<div>
	<div style="width: 100%; margin-top: 24px;">
		<h4 style="margin-bottom: 12px;">Incendi gestiti il <?= $today;?> / sottotipologia</h4>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<?php echo $images[2] != 'empty' ? Html::img($images[2], ['width'=>300]) : ''; ?>
		</div>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<?php 
			$n = 0;
			foreach ($incendi_attivi_tipologia as $row) {
				?>
				<div style="font-size:11px; margin: 0">
                    <p class="" style="width: 36px; height: 10px; display: inline-block; float: left; background-color: <?= $colors[$n];?>">
                    </p>
                    <p style="float: left; margin-left: 12px; line-height: 12px;">
                    	<b><?= $row['tipologia'];?></b>: <?= $row['conteggio'];?>
                	</p>
                </div>
				<?php
				$n++;
			}
			?>
		</div>
	</div>

	<div style="width: 100%; margin-top: 24px;">
		<h4 style="margin-bottom: 12px;">Incendi gestiti il <?= $today;?> / ente gestore</h4>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<?php echo $images[3] != 'empty' ? Html::img($images[3], ['width'=>300]) : ''; ?>
		</div>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<?php 
			$n = 0;
			foreach ($incendi_attivi_ente_gestore as $row) {
				?>
				<div style="font-size:11px; margin: 0">
                    <p class="" style="width: 36px; height: 10px; display: inline-block; float: left; background-color: <?= $colors[$n];?>">
                    </p>
                    <p style="float: left; margin-left: 12px; line-height: 12px;">
                    	<b><?= $row['descrizione'];?></b>: <?= $row['conteggio'];?>
                	</p>
                </div>
				<?php
				$n++;
			}
			?>
		</div>
	</div>

	<div style="width: 100%; margin-top: 24px;">
		<h4 style="margin-bottom: 12px;">Incendi gestiti il <?= $today;?> / provincia</h4>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<?php echo $images[4] != 'empty' ? Html::img($images[4], ['width'=>300]) : ''; ?>
		</div>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<?php 
			$n = 0;
			foreach ($incendi_provincia as $row) {
				?>
				<div style="font-size:11px; margin: 0">
                    <p class="" style="width: 36px; height: 10px; display: inline-block; float: left; background-color: <?= $colors[$n];?>">
                    </p>
                    <p style="float: left; margin-left: 12px; line-height: 12px;">
                    	<b><?= $row['sigla'];?></b>: <?= $row['conteggio'];?>
                	</p>
                </div>
				<?php
				$n++;
			}
			?>
		</div>
	</div>

	<div class="break"></div>

	<div style="width: 100%; margin-top: 24px;">
		<h4 style="margin-bottom: 12px;">Incendi boschivi gestiti il <?= $today;?> / provincia</h4>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<?php echo $images[5] != 'empty' ? Html::img($images[5], ['width'=>300]) : ''; ?>
		</div>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<?php 
			$n = 0;
			foreach ($incendi_boschivi_provincia as $row) {
				?>
				<div style="font-size:11px; margin: 0">
                    <p class="" style="width: 36px; height: 10px; display: inline-block; float: left; background-color: <?= $colors[$n];?>">
                    </p>
                    <p style="float: left; margin-left: 12px; line-height: 12px;">
                    	<b><?= $row['sigla'];?></b>: <?= $row['conteggio'];?>
                	</p>
                </div>
				<?php
				$n++;
			}
			?>
		</div>
	</div>

	<div style="width: 100%; margin-top: 24px;">
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<h4>MEZZI IMPIEGATI (PICKUP / AUTOBOTTI)</h4>
			<?php 
			$n = 0;
			foreach ($mezzi_impiegati as $row) {
				?>
				<div style="font-size:11px; margin: 0">
                    <p class="" style="width: 36px; height: 10px; display: inline-block; float: left; background-color: <?= $colors[$n];?>">
                    </p>
                    <p style="float: left; margin-left: 12px; line-height: 12px;">
                    	<b><?= $row['sigla'];?></b>: <?= $row['conteggio'];?>
                	</p>
                </div>
				<?php
				$n++;
			}
			?>
		</div>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<h4>LANCI ELICOTTERO</h4>
			<p class="big_number"><?= $lanci_elicottero[0]['numero_lanci'];?></p>
		</div>
	</div>

	<div style="width: 100%; margin-top: 24px;">
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<h4>INCENDI CON INTERVENTO DEL MEZZO AEREO</h4>
			<p class="big_number"><?= $incendi_mezzo_aereo[0]['conteggio'];?></p>
		</div>
		<div style="width: 48%; margin: 0; float: left; display: inline-block;">
			<h4>ORE DI VOLO</h4>
			<p class="big_number"><?= $ore_di_volo[0]['ore_di_volo'];?></p>
		</div>
	</div>
</div>
