<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$confirmlink = Yii::$app->urlManagerApi->createUrl(['auth/confirm', 'token' => $user->auth_key]);
?>
<div class="registration-confirm">
    <p>Gentile utente,</p>

    <p>
        ti ringraziamo per avere aderito alla nostra iniziativa che tramite la nostra <strong>APP <?= Yii::$app->params['APP_NAME'];?></strong>
        si propone di controllare e monitorare l'ambiente e il territorio della Regione <?= Yii::$app->params['REGION_NAME'];?>.
    </p>

    <p>
        Cliccare sul link seguente per completare la registrazione: <?= Html::a('Conferma', $confirmlink) ?>
    </p>

    <p>
        Grazie ancora per il Vostro aiuto,<br>
        <?= Yii::$app->params['MAIL_APP_SENDER'];?>
    </p>

</div>
