<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p>Salve <?= Html::encode($user->username) ?>,</p>

    <p>A seguito della vostra richiesta di rinnovo delle credenziali d'accesso al sistema <?= Yii::$app->params['APP_NAME'];?>, di seguito le informazioni e le istruzioni necessarie per procedere al rinnovo della password:</p>

    <p>Cliccare sul link per impostare una nuova password:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>

    <p>
    	Si prega di notare che: 
    	<ul>
			<li>Le informazioni riguardanti l'account in gestione devono rimanere riservate, in quanto personali e in grado di inviare segnalazioni alla SOR in nome e per conto del volontario.</li>
			<li>Questo messaggio è automatizzato, si prega di non rispondere.</li> 
		</ul>
    </p>

    <p>Regione <?= Yii::$app->params['REGION_NAME'];?> - <?= Yii::$app->params['MAIL_APP_SENDER'];?></p>
</div>
