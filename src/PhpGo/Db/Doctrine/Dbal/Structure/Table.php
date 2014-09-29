<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 14:50
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure;

use Doctrine\DBAL\Schema\Schema;
use PhpGo\Db\Doctrine\Dbal\Structure\Field\FieldAbstract;
use PhpGo\Db\Doctrine\Dbal\Structure\Field\FieldFactory;

class Table implements ConfigAbleInterface
{
    protected $name;
    protected $fields;
    /** @var  Structure */
    protected $structure;
    /** @var  Table[] */
    protected $belongToTables;

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param mixed $structure
     */
    public function setStructure(Structure $structure)
    {
        $this->structure = $structure;
    }

    public function addField(FieldAbstract $field)
    {
        $this->fields[$field->getName()] = $field;
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

    public function createSchema(Schema $schema)
    {
        return $schema->createTable($this->name);
    }

    public function addBelongTo(Table $table)
    {
        $this->belongToTables[$table->getName()] = $table;

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
}
