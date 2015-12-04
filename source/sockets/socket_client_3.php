<?php
require '../application/bootstrap.php';

class Jequitinator extends php_user_filter
{
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $s = $bucket->data;
            if (rand(5, 7) === 7) {
                $bucket->data = wordwrap($s,strlen($s) * .7," [Jequiti] ");
            }
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }
}
stream_filter_register('jequiti', 'Jequitinator');
$client = stream_socket_client("tcp://127.0.0.1:32223", $errno, $errstr, 30);
stream_filter_append($client, 'jequiti');
if (!$client) {
    echo "$errstr ($errno)" . PHP_EOL;
} else {
    fwrite($client, "Mah que horas são Lombardi?" . PHP_EOL);
    while (!feof($client)) {
        echo fgets($client, 1024);
    }
    fclose($client);
}