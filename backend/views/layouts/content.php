<?php
use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;
use common\models\app\AppConfig;
?>
<div class="content-wrapper">
    <section class="content-header">
        <?=
        Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        ) ?>
    </section>

    <section class="content">
        <?= Alert::widget() ?>
        <?= $content ?>
    </section>

    
</div>

<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <strong>Version</strong> 3.1 
        <?php if(Yii::$app->FilteredActions->show_footer_last_sync_mgo) { ?>
            <strong>Ultimo aggiornamento dati MGO</strong> <?php 
                $conf = AppConfig::findOne(['key'=>'last_mgo_sync']);

                if(!$conf) {
                    echo " - ";
                } else {
                    try {
                        echo json_decode($conf->value,true)['date'];
                    } catch(\Exception $e) {
                        echo " - ";
                    }
                }
            ?>
        <?php } ?>
    </div>
    <strong>Copyright &copy; 2018 <a href="#"></a>.</strong> Tutti i diritti riservati.
</footer>