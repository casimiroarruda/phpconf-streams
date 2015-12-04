<?php
require 'SQLiteWrapper.php';
stream_wrapper_register('sql', 'SQLiteWrapper');
$configuration = [
    'sql' => [
        'attributes' => [PDO::ATTR_AUTOCOMMIT => true],
        'path' => '/tmp'
    ]
];
$context = stream_context_create($configuration);
$streamAddress = 'sql://db.sqlite/xpto';
file_put_contents($streamAddress, 'Oi, eu sou o Goku', null, $context);
file_put_contents($streamAddress, 'Não importa seu VERME!', null, $context);
echo file_get_contents($streamAddress, null, $context);