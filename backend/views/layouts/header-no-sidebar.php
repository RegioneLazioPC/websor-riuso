<?php
use common\models\UtlOperatorePc;
use common\models\UtlSegnalazione;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */
$js = "
$(document).ready(function(){
        
})";

$this->registerJs($js, $this::POS_READY);

?>
<header class="main-header">
    <?= Html::a(
        '<span class="logo-mini" style="height: 54px;">#W<b>S</b></span><span class="">'
        . Yii::$app->name .
        '</span>',
        Yii::$app->homeUrl,
        [ 
            'class' => 'logo', 
            'style' => 'height: 54px !important;' 
        ]
    ) ?>
    <nav class="navbar navbar-static-top" role="navigation" style="height: 54px;">
        <div class="main-title hidden-xs hidden-md">
            <?php echo Html::img( '@web/images/logo_regione.gif' ); ?>
            <span class="text-uppercase">Protezione Civile Regione Lazio</span>
            <?php echo Html::img( '@web/images/logo.png' ); ?>
        </div>
    </nav>
</header>
