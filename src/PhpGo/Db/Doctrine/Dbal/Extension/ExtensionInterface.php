<?php
/**
 * User: dongww
 * Date: 2014-10-7
 * Time: 14:39
 */

namespace PhpGo\Db\Doctrine\Dbal\Extension;

use PhpGo\Db\Doctrine\Dbal\Structure\Table;

interface ExtensionInterface
{
    public function boot(Table $table);

    public function getName();
}
