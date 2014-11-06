<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpGo\Db\Doctrine\Dbal\Structure\Structure;
use PhpGo\Db\Doctrine\Dbal\Structure\Table;

//$structure = Structure::createFromYaml(__DIR__ . '/config/structure.yml');

//print_r($structure->getConfig());
//$table = Table::createFromStructure('user', $structure);
//print_r($table);
//print_r($structure->getTables());

$conn = require __DIR__ . '/config/config.php';
$diff = new \PhpGo\Db\Doctrine\Dbal\Tool\SchemaChecker(
    $conn
);

$sqlArr = $diff->getDiffSql(__DIR__ . '/config/structure.yml');

foreach ($sqlArr as $sql) {
    echo $sql . PHP_EOL;
    $conn->query($sql);
}
