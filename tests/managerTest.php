<?php
use PhpGo\Db\Doctrine\Dbal\Structure\Structure;

require_once __DIR__ . '/../vendor/autoload.php';

$conn = require __DIR__ . '/config/config.php';

$structure = Structure::createFromYaml(__DIR__ . '/config/structure.yml');

$mf = new \PhpGo\Db\Doctrine\Dbal\Manager\ManagerFactory($conn, $structure);

$gm = $mf->getManager('goods');

//$bean = $gm->createBean([
//    'category_id' => 2,
//    'name'        => 'apple',
//]);

//$bean = $gm->get(2);
//
//$bean->name = 'abc';
//$bean->price = 2;
//
//$gm->store($bean);

$cm = $mf->getManager('category');
$goods = $cm->getMany(2, 'goods');

print_r($goods);