<?php
require '../application/bootstrap.php';
$client = stream_socket_client("tcp://127.0.0.1:32223", $errno, $errstr, 30);
if (!$client) {
    echo "$errstr ($errno)".PHP_EOL;
} else {
    fwrite($client, "Mah que horas são Lombardi?".PHP_EOL);
    while (!feof($client)) {
        echo fgets($client, 1024);
    }
    fclose($client);
}