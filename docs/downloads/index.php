<?php

$command = isset($_GET['command']) ? $_GET['command'] : '--version';

echo '<pre>';


exec('php csmt.phar ' . escapeshellarg($command), $output, $return_var);

foreach($output as $out) {
    echo $out . PHP_EOL;
}

echo '</pre>';

