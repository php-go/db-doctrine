<?php
require_once __DIR__ . '/../../vendor/autoload.php';

$config = new \Doctrine\DBAL\Configuration();
//..
$connectionParams = [
    'dbname'   => 'db_wrap',
    'user'     => 'root',
    'password' => '',
    'host'     => 'localhost',
    'driver'   => 'pdo_mysql',
    'charset'  => 'utf8',
];
return \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
