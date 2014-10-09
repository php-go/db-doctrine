<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 15:34
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

use PhpGo\Db\Doctrine\Dbal\Structure\Relation\Relation;
use PhpGo\Db\Doctrine\Dbal\Structure\Table;

class RelationField extends IntegerField
{
    protected $relationTable;

    public function __construct(Table $relationTable, Table $table, $name = '')
    {
        $this->relationTable = $relationTable;

        $this->setTable($table)
            ->setIndex(false)
            ->setUnique(false)
            ->setRequired(false);

        if ($name) {
            $this->name = $name;
        }
    }

    public function getName()
    {
        if (!$this->name) {
            $this->name = Relation::getForeignKey(
                $this->relationTable->getName()
            );
        }

        return $this->name;
    }

    public function getRelationTable()
    {
        return $this->relationTable;
    }

    public function getRelationTableName()
    {
        return $this->relationTable->getName();
    }
}
