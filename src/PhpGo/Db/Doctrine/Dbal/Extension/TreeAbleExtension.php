<?php
/**
 * User: dongww
 * Date: 2014-10-7
 * Time: 14:38
 */

namespace PhpGo\Db\Doctrine\Dbal\Extension;

use PhpGo\Db\Doctrine\Dbal\Structure\Field\IntegerField;
use PhpGo\Db\Doctrine\Dbal\Structure\Field\StringField;
use PhpGo\Db\Doctrine\Dbal\Structure\Table;

class TreeAbleExtension implements ExtensionInterface
{
    public function getName()
    {
        return 'tree_able';
    }

    public function boot(Table $table)
    {
        $table
            ->addField(new StringField('name', $table))
            ->addField(new IntegerField('sort', $table))
            ->addField(new StringField('path', $table))
            ->addField(new IntegerField('level', $table))
            ->addBelongTo($table, 'parent_id');
    }
}
