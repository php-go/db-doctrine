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
use PhpGo\Db\Doctrine\Dbal\Structure\Relation\Relation;

class Table implements ConfigAbleInterface
{
    protected $name;
    /** @var  FieldAbstract[] */
    protected $fields = [];
    /** @var  Structure */
    protected $structure;

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

    /**
     * @param $name
     * @return FieldAbstract
     * @throws \Exception
     */
    public function getField($name)
    {
        if (!isset($this->fields[$name])) {
            throw new \Exception("字段 $name 不存在。");
        }

        return $this->fields[$name];
    }

    public function hasBelongToTable($name)
    {
        $foreignKey    = Relation::getForeignKey($name);
        $relationField = $this->getField($foreignKey);

        if ($relationField instanceof RelationField) {
            return true;
        }

        return false;
    }

    public function getBelongToTable($name)
    {
        $foreignKey    = Relation::getForeignKey($name);
        $relationField = $this->getField($foreignKey);

        if (!($relationField instanceof RelationField)) {
            throw new \Exception("$name 不是关联表");
        }

        return $relationField->getRelationTable();
    }

    public function hasOneToManyTable($name)
    {
        $relationTable = $this->structure->getTable($name);

        if ($relationTable->hasBelongToTable($this->getName())) {
            return true;
        }

        return false;
    }

    public function hasManyToManyTable($name)
    {
        $manyMany = $this->structure->getConfig()['many_many'];

        foreach ($manyMany as $mm) {
            if (in_array($this->getName(), $mm) && in_array($name, $mm)) {
                return true;
            }
        }

        return false;
    }
}
