<?php
use yii\helpers\Html;

?>
<div class="tbl" style="font-family: 'Arial'; color: #000000 !important;">
    <div>
        <div style="float: left; width: 40%">
            <?= $nome_file;?>
        </div>
        <div style="float: right; text-align: right; width: 40%">
            EXPORT ACCESSI
        </div>
    </div>
    <table style="width: 100%;border-collapse:collapse; font-size: 9px; ">
        <tbody>
            <tr style="border: 1px solid #000;">
                <td style="font-weight: bold; font-style: italic; color: #000; width: 25%;" >Username</td>
                <td style="font-weight: bold; font-style: italic; color: #000; width: 25%;" >Data</td>
                <td style="font-weight: bold; font-style: italic; color: #000; width: 25%;" >IP</td>
                <td style="font-weight: bold; font-style: italic; color: #000; width: 25%;" >AZIONE</td>
            </tr>
            <?php
            $n = 0;
            foreach ($value as $record) {
                $n++;
                ?>
                <tr style="border: 1px solid #000;">
                    <td style="width: 25%;" ><?= $record['username'];?></td>
                    <td style="width: 25%;" ><?= $record['datetime'];?></td>
                    <td style="width: 25%;" ><?= $record['ip'];?></td>
                    <td style="width: 25%;" ><?= $record['action'];?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</div>

