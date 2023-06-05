<?php

$current = require __DIR__ . '/common/config/params-local.php';
$dev_env = require __DIR__ . '/environments/dev/common/config/params-local.php';

foreach ($current as $key => $value) {
    if (!isset($dev_env[$key])) {
        echo "MANCA: " . $key . "\n";
    }
}
