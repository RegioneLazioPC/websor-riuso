<?php

use yii\bootstrap\Tabs;
use yii\bootstrap\Modal;

use common\utils\MasMessageManager;


?>
<h2>Anteprima</h2>
<?php
$element = [];

if($model->channel_mail) {
    $element[] = [
        'label' => 'Email',
        'content' => '<div class="panel panel-default" style="margin-top: 10px"><div class="panel-heading"><h3 class="panel-title">' . $model->title . '</h3></div>
        <div class="panel-content" style="padding: 20px">
        ' . MasMessageManager::getPreview($model, $model->template, 0) . '
        </div>
        </div>',
        'options' => ['id' => 'email_show_text'],
    ];
}
if($model->channel_pec) {
    $element[] = [
        'label' => 'Pec',
        'content' => '<div class="panel panel-default" style="margin-top: 10px"><div class="panel-heading"><h3 class="panel-title">' . $model->title . '</h3></div>
        <div class="panel-content" style="padding: 20px">
        ' .MasMessageManager::getPreview($model, $model->template, 0)  . '
        </div>
        </div>',
        'options' => ['id' => 'pec_show_text'],
    ];
}
if($model->channel_fax) {
    $element[] = [
        'label' => 'Fax',
        'content' => '<div class="panel panel-default" style="margin-top: 10px"><div class="panel-heading"><h3 class="panel-title">' . $model->title . '</h3></div>
        <div class="panel-content" style="padding: 20px">
        ' .MasMessageManager::getPreview($model, $model->template, 2)  . '
        </div>
        </div>',
        'options' => ['id' => 'fax_show_text'],
    ];
}
if($model->channel_sms) {
    $element[] = [
        'label' => 'Sms',
        'content' => '<div class="panel panel-default" style="margin-top: 10px"><div class="panel-heading"><h3 class="panel-title">' . $model->title . '</h3></div>
        <div class="panel-content" style="padding: 20px">
        ' .MasMessageManager::getPreview($model, $model->template, 3)  . '
        </div>
        </div>',
        'options' => ['id' => 'sms_show_text'],
    ];
}
if($model->channel_push) {
    $element[] = [
        'label' => 'Push notification',
        'content' => '<div class="panel panel-default" style="margin-top: 10px"><div class="panel-heading"><h3 class="panel-title">' . $model->title . '</h3></div>
        <div class="panel-content" style="padding: 20px">
        ' .MasMessageManager::getPreview($model, $model->template, 4)  . '
        </div>
        </div>',
        'options' => ['id' => 'push_show_text'],
    ];
}
?>

<?php 

    echo Tabs::widget([
    'items' => $element
    ]);
?>