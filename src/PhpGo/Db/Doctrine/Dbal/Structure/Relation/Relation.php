<?php
/**
 * User: dongww
 * Date: 2014-9-29
 * Time: 14:52
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Relation;


use PhpGo\Db\Doctrine\Dbal\Structure\Field\IntegerField;
use PhpGo\Db\Doctrine\Dbal\Structure\Table;

class Relation
{
    public static function createManyToManyTable(Table $table1, Table $table2)
    {
        $relation = [$table1->getName(), $table2->getName()];
        sort($relation);

        $table = new Table();
        $table->setName($relation[0] . '_' . $relation[1])
            ->addBelongTo($table1)
            ->addBelongTo($table2);

        return $table;
    }
}
