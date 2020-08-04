<?php
use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;

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
    </div>
    <strong>Copyright &copy; 2018 <a href="#"></a>.</strong> Tutti i diritti riservati.
</footer>