<?php 
use yii\helpers\Html;
?>
<!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>       
        <style>
            h3{
                font-family: monospace;
            }
            table *{
                font-size: 12px;
                text-align: left;
                font-family: sans-serif;
            }
            table tr td {
                font-weight: 300;
            }
    </style>    
    </head>
    <body class="hold-transition skin-black-light sidebar-mini theme_pc">
    
        <h3>Report per messaggio <?php echo $invio->message->title;?>, canale: <?php echo $channel;?></h3>
        <table style="width: 100%" summary="Risultati">
            <thead>
                <tr style="background-color: #e8fdff;">
                    <th scope="col" style="width: 25%">Riferimento</th>
                    <th scope="col" style="width: 25%">Contatto</th>
                    <th scope="col" style="width: 11%">Inviato</th>
                    <th scope="col" style="width: 13%">Stato invio</th>
                    <th scope="col" style="width: 13%">Data invio</th>
                    <th scope="col" style="width: 13%">Data feedback</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $n = 0;
                foreach ($result as $dest) {
                    $n++;

                    $style = 'border-top: 1px solid #d6d6d6';
                    if($n%2 == 0) $style .= '; background-color: #eee';
                    $send_ = json_decode( $dest['invii'], true );

                    foreach ($send_ as $row) {
                        ?>
                        <tr style="<?php echo $style;?>">
                            <td><?php echo Html::encode($dest['valore_riferimento']);?></td>
                            <td><?php echo Html::encode($dest['valore_rubrica_contatto']);?></td>
                            <td><?php echo ($dest['inviato'] > 0) ? 'Si' : 'No';?></td>
                            <td><?php echo (!empty($row['status'])) ? \common\models\MasSingleSend::getStatoByNumber( $row['status'] ) : '';?></td>
                            <td><?php echo (!empty($row['status'])) ? ((!empty($row['sent'])) ? date('d-m-Y H:i:s', $row['sent']) : '') : '';?></td>
                            <td><?php echo (!empty($row['status'])) ? ((!empty($row['feedback'])) ? date('d-m-Y H:i:s', $row['feedback']) : '') : '';?></td>
                        </tr>
                        <?php
                    }
                    
                                        
                }
                ?>
            </tbody>
        </table>
    </body>
</html>