<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 14:50
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure;

use PhpGo\Db\Doctrine\Dbal\Structure\Field\FieldAbstract;
use PhpGo\Db\Doctrine\Dbal\Structure\Field\FieldFactory;
use PhpGo\Db\Doctrine\Dbal\Structure\Field\RelationField;

class Table implements ConfigAbleInterface
{
    protected $name;
    /** @var  FieldAbstract[] */
    protected $fields;
    /** @var  Structure */
    protected $structure;
    protected $schemaTable = null;

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
     * @param  Structure $structure
     * @return Table
     */
    public function setStructure(Structure $structure)
    {
        $this->structure = $structure;

        return $this;
    }

    public function addField(FieldAbstract $field)
    {
        $this->fields[$field->getName()] = $field;

        return $this;
    }

    public function __construct()
    {
    }

    public static function createFromStructure($name, Structure $structure)
    {
        $config = $structure->getConfig();

        if (!array_key_exists($name, $config['tables'])) {
            throw new \Exception("结构配置文件中没有 $name 这个表。");
        }

        $table = new static();
        $table->setName($name);
        $table->setStructure($structure);

        foreach ($table->getConfig()['fields'] as $name => $field) {
            $table->addField(
                FieldFactory::create($name, $table)
            );
        }

        return $table;
    }

    public function addBelongTo(Table $table, $foreignKey = '')
    {
        $this->addField(
            new RelationField($table, $this, $foreignKey)
        );

        return $this;
    }

    public function getConfig()
    {
        $config = $this->structure->getConfig();

        return $config['tables'][$this->name];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFields()
    {
        return $this->fields;
    }
}
