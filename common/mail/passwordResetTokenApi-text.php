<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */


?>
Salve <?= $username ?>,
<p>ti è stata inviata questa mail perchè hai richiesto di recuperare la password di accesso ad <?= Yii::$app->params['APP_NAME'];?>.</p>
<p>
    Per fare in modo che funzioni correttamente la procedura di reset della password:<br>
    1. verificare che la APP <?= Yii::$app->params['APP_NAME'];?> sul tuo smartphone sia chiusa<br>
    2. cliccare sul link seguente per impostare una nuova password: <?= $url; ?>
</p>
