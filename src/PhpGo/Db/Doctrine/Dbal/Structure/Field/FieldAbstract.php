<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 16:20
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

use PhpGo\Db\Doctrine\Dbal\Structure\ConfigAbleInterface;
use PhpGo\Db\Doctrine\Dbal\Structure\Table;

abstract class FieldAbstract implements ConfigAbleInterface, FieldInterface
{
    protected $name;
    /** @var  Table */
    protected $table;
    protected $index;
    protected $unique;
    protected $required;

    public function __construct($name, Table $table, $required = false, $index = false, $unique = false)
    {
        $this->setName($name)
            ->setTable($table)
            ->setIndex($index)
            ->setUnique($unique)
            ->setRequired($required);
    }

    /**
     * @return mixed
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param  mixed $required
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = (bool)$required;

        return $this;
    }

    /**
     * @return mixed
     */
    public function isIndex()
    {
        return $this->index;
    }

    /**
     * @param  mixed $index
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = (bool)$index;

        return $this;
    }

    /**
     * @return mixed
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * @param  mixed $unique
     * @return $this
     */
    public function setUnique($unique)
    {
        $this->unique = (bool)$unique;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     * @return mixed|\PhpGo\Db\Doctrine\Dbal\Structure\Table
     */
    public function setTable(Table $table)
    {
        $this->table = $table;

        return $this;
    }

    public function getConfig()
    {
        return $this->table->getConfig()['fields'][$this->name];
    }
}
