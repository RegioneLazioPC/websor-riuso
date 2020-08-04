<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$confirmlink = Yii::$app->urlManagerApi->createUrl(['auth/confirm', 'token' => $user->auth_key]);
?>

Gentile utente,
<br>
ti ringraziamo per avere aderito alla nostra iniziativa che tramite la nostra <strong>APP <?= Yii::$app->params['APP_NAME'];?></strong>
si propone di controllare e monitorare l'ambiente e il territorio della Regione <?= Yii::$app->params['REGION_NAME'];?>.
<br><br>
Clicca sul link seguente per completare la registrazione: <?= Html::a('Conferma', $confirmlink) ?>
<br><br>
Grazie ancora per il Vostro aiuto,<br>
<?= Yii::$app->params['MAIL_APP_SENDER'];?>

