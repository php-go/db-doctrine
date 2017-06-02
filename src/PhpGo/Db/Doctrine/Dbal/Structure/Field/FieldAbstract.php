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
    /** @var  array */
    protected $options;

    public function __construct($name, Table $table, $options = [])
    {
        $this->setName($name)
            ->setTable($table);

        $this->setOptions($options);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOption($key)
    {
        return $this->options[$key]??null;
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    public function getDoctrineOptions()
    {
        return [
            'notnull'       => $this->getOption('required'),
            'length'        => $this->getOption('length'),
            'default'       => $this->getOption('default '),
            'autoincrement' => $this->getOption('autoincrement '),
            'fixed'         => $this->getOption('fixed '),
            'precision'     => $this->getOption('precision '),
            'scale'         => $this->getOption('scale '),
            'unsigned'      => $this->getOption('unsigned '),
            'comment'       => $this->getOption('comment '),
        ];
    }

    /**
     * @param  mixed $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param  mixed $table
     *
     * @return mixed|Table
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
