<?php
require '../application/bootstrap.php';
$socket = stream_socket_server("tcp://127.0.0.1:32223", $errno, $errstr);
if (!$socket) {
    echo "{$errstr} ({$errno})" . PHP_EOL;
} else {
    while ($conn = stream_socket_accept($socket)) {
        echo fread($conn,1024);
        $horas = date('G').' horas com '.((int)date('i')).' minutos';
        fwrite($conn, "Agora são {$horas} Silviooo.". PHP_EOL);
        fclose($conn);
    }
    fclose($socket);
}