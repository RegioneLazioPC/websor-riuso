<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

if (Yii::$app->controller->action->id === 'login' ||
Yii::$app->controller->action->id === 'request-password-reset' || 
Yii::$app->controller->action->id === 'reset-password') {

    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {

    if(!Yii::$app->user->identity) {
        ?>
        <script type="text/javascript">
            window.location = "<?= Yii::$app->urlManager->createUrl('site/login') ?>"
        </script>
        <?php
        exit();
    }
    
    if (class_exists('backend\assets\AppAsset')) {
        backend\assets\AppAsset::register($this);
    } else {
        app\assets\AppAsset::register($this);
    }

    dmstr\web\AdminLteAsset::register($this);

    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>

        <script>
            var apiurl= "<?php echo Yii::$app->urlManagerApi->baseUrl; ?>";
            var siteurl= "<?php echo Yii::$app->urlManager->baseUrl; ?>";
            var google_map_key= "<?php echo Yii::$app->params['google_key']; ?>";
        </script>
        
    </head>
    <body class="hold-transition skin-black-light sidebar-mini theme_pc">
    <?php $this->beginBody() ?>
    <div class="wrapper">

        <?= $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>

        <?= $this->render(
            'left.php',
            ['directoryAsset' => $directoryAsset]
        )
        ?>

        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        ) ?>

    </div>

    <?php $this->endBody() ?>
        <div id="loading-spin" style="display: none; width: 100vw; height: 100vh; position: fixed; top: 0; left: 0; background-color: rgba(0,0,0,.4); z-index: 9991;">
            <em class="fa fa-spinner fa-spin" style="margin-left: -15px; margin-top: -15px;color: #fff; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); font-size: 30px;"></em>
            <p style="position: absolute; top: 50%; margin-top: 25px; width: 100vw;text-align: center; color: #fff">Attendi il completamento della richiesta</p>
        </div>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>
