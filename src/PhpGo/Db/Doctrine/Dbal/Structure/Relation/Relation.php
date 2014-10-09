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
        $table = new Table();
        $table->setName(self::getManyToManyTableName($table1, $table2))
            ->addBelongTo($table1)
            ->addBelongTo($table2);

        return $table;
    }

    public static function getManyToManyTableName(Table $table1, Table $table2)
    {
        $relation = [$table1->getName(), $table2->getName()];
        sort($relation);

        return $relation[0] . '_' . $relation[1];
    }

    public static function getForeignKey($relationTableName)
    {
        return $relationTableName . '_id';
    }
}
