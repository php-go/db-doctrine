<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 15:34
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

use Doctrine\DBAL\Schema\Table as SchemaTable;
use PhpGo\Db\Doctrine\Dbal\Structure\Table;

class RelationField extends IntegerField
{
    protected $relationTable;

    public function __construct(Table $relationTable, Table $table)
    {
        $this->relationTable = $relationTable;

        $this->setTable($table)
            ->setIndex(false)
            ->setUnique(false)
            ->setRequired(false);
    }

    public function getName()
    {
        if (!$this->name) {
            $this->name = $this->relationTable->getName() . '_id';
        }
        return $this->name;
    }

    public function getRelationTable()
    {
        return $this->relationTable;
    }
}
