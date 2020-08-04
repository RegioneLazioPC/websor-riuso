<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

//$resetLink = "easyalert://activation/".$user->password_reset_token;
//$resetLink = Yii::$app->urlManagerApi->createAbsoluteUrl(['utenti/deeplink', 'token' => $user->password_reset_token]);

?>
<div class="password-reset">
    <p>Salve <?= Html::encode($username) ?>,</p>

    <p>ti è stata inviata questa mail perchè hai richiesto di recuperare la password di accesso ad <?= Yii::$app->params['APP_NAME'];?>.</p>
    <p>Per fare in modo che funzioni correttamente la procedura di reset della password:</p>
    <p>
        <ul style="list-style-type: decimal;">
            <li>verificare che la app <?= Yii::$app->params['APP_NAME'];?> sul tuo smartphone sia chiusa</li>
            <li>cliccare sul link seguente per impostare una nuova password: <?= Html::a('Imposta nuova password', $url) ?></li>
        </ul>
    </p>

</div>
