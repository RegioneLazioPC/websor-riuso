<?php

use common\components\FilteredActions;
use common\models\UtlOperatorePc;
use common\models\UtlSegnalazione;
use yii\helpers\Html;

use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

$lastSegnalazioni = UtlSegnalazione::getLastSegnalazione();

$can_view_segnalazioni = Yii::$app->user->can('listSegnalazioni');


if ($can_view_segnalazioni) {

    $js = "

    $(document).ready(function(){
        var segnalazioni_ids = [];
        var n_segnalazioni = 0;

        var el = $('#count_segnalazioni');
        var _a = $('#blinkeblunk');

        function beep() {
            var audio = new Audio('" . Url::home(true) . "beep.mp3');
            audio.play();
        }

        function loadSegnalazioni() {
            
            $.get( '" . Url::to(['segnalazione/count']) . "', function( data ) { 
                
                n_segnalazioni = data.length;
                var bip = false;
                var new_segnalazioni = [];

                data.map( function( seg ) {
                    if( segnalazioni_ids.indexOf( seg.id ) == -1 ) bip = true;

                    new_segnalazioni.push(seg.id);
                } );

                segnalazioni_ids = new_segnalazioni;
                // se ci sono id nuovi faccio il suono
                
                if(bip) beep(); 

                el.html( n_segnalazioni );
                if(parseInt(n_segnalazioni) > 0) {
                    _a.addClass('blink');
                } else {
                    _a.removeClass('blink');
                }
            });
        }
        
        loadSegnalazioni();

        var el = $('#count_segnalazioni')
        setInterval(function(){
            loadSegnalazioni();
        }, 60000);


        
    })

    
    ";

    $this->registerJs($js, $this::POS_READY);
}



?>

<header class="main-header">



    <?= Html::a('<span class="logo-mini" style="height: 83px;">#W<strong>S</strong></span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="main-title hidden-xs hidden-md" <?php if (!$can_view_segnalazioni) echo 'style="padding-top: 16px;"'; ?>>
            <?php echo Html::img('@web/images/logo_regione.gif'); ?>
            <span class="text-uppercase">Protezione Civile <?php echo Yii::$app->FilteredActions->getAppName() ?></span>
            <?php echo Html::img('@web/images/logo.png'); ?>
            <p class="hidden-xs hidden-sm hidden-md" style="font-size: 20px;">
                <?php if ($can_view_segnalazioni) {
                ?>
                    <a class="" id="blinkeblunk" href="<?= Yii::$app->urlManager->createUrl('/segnalazione') ?>">
                        <strong id="count_segnalazioni">0</strong> segnalazioni non lavorate
                    </a>
                <?php }
                ?>
            </p>
        </div>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <li class="dropdown notifications-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <em class="fa fa-bell-o fa-pulse"></em>
                        <span class="label label-warning"><?php echo @count($lastSegnalazioni); ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="header">Ci sono <?php echo @count($lastSegnalazioni) ?> nuove segnalazioni</li>
                        <li>
                            <ul class="menu">

                                <?php 
                                $tipi_segnalazioni = \common\models\UtlTipologia::find()->asArray()->all();
                                $tipi_segnalazioni = \yii\helpers\ArrayHelper::index($tipi_segnalazioni, 'id');
                                
                                foreach ($lastSegnalazioni as $segnalazione) :
                                    $nome_tipo = $segnalazione->tipologia_evento; 
                                    try {
                                        if(empty($segnalazione->tipologia_evento)) {
                                            $nome_tipo = 'SOS';
                                        } else {
                                            $tipo_segnalazione = $tipi_segnalazioni[$segnalazione->tipologia_evento];
                                        
                                            $nome_tipo = Html::encode($tipo_segnalazione['tipologia']);
                                        }
                                        
                                        
                                    } catch(\Exception $e) {
                                        throw $e;
                                    }
                                    ?>

                                    <li>
                                        <a href="<?= Yii::$app->urlManager->createUrl('/segnalazione/view?id=' . $segnalazione->id) ?>">
                                            <em class="fa fa-warning text-yellow"></em>
                                            <strong><?php echo $nome_tipo; ?></strong> - <?= Html::encode(@$segnalazione->note); ?>
                                            <p><?php echo Html::encode(@$segnalazione->dataora_segnalazione); ?></p>
                                        </a>
                                    </li>

                                <?php endforeach; ?>

                            </ul>
                        </li>
                        <li class="footer"><?php echo Html::a('Vedi tutte', ['/segnalazione/index']) ?>Vedi tutte</a></li>
                    </ul>
                </li>

                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="<?= $directoryAsset ?>/img/avatar5.png" class="user-image" alt="User Image" style="float: none; margin-top: -5px !important;" />
                        <span class="hidden-xs">

                            <?php
                            if (!empty(Yii::$app->user->identity->id)) :
                                $operatore = UtlOperatorePc::find()->where(['iduser' => Yii::$app->user->identity->id])->one();
                                echo Html::encode(@$operatore->anagrafica->nome . ' ' . @$operatore->anagrafica->cognome);
                            endif;
                            ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu">

                        <li class="user-header">
                            <p>
                                <?php echo Html::encode(@$operatore->anagrafica->nome . ' ' . @$operatore->anagrafica->cognome); ?><br>
                                Matricola N. <?php echo Html::encode(@Yii::$app->user->identity->username); ?><br>
                                <?php echo Html::encode(@$operatore->ruolo); ?>
                                <small>Utente attivo</small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-left">

                            </div>
                            <div class="pull-left">
                                <?= Html::a(
                                    'Profilo',
                                    ['/profile/view'],
                                    [
                                        'class' => 'btn btn-default btn-flat',
                                    ]
                                ) ?>
                            </div>
                            <div class="pull-right">
                                <?= Html::a(
                                    'Esci',
                                    ['/site/logout'],
                                    [
                                        'class' => 'btn btn-default btn-flat',
                                        'data' => [
                                            'confirm' => 'Sicuro di voler effettuare il logout',
                                            'method' => 'post',
                                        ]
                                    ]
                                ) ?>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>