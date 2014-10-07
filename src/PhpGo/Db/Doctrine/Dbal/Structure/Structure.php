<?php
/**
 * User: dongww
 * Date: 14-5-29
 * Time: 下午2:44
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure;

use PhpGo\Db\Doctrine\Dbal\Extension\ExtensionInterface;
use PhpGo\Db\Doctrine\Dbal\Extension\TimestampAbleExtension;
use PhpGo\Db\Doctrine\Dbal\Extension\TreeAbleExtension;
use PhpGo\Db\Doctrine\Dbal\Structure\Relation\Relation;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class Structure implements ConfigAbleInterface
{
    protected $config;
    /**
     * @var Table[]
     */
    protected $tables;
    /** @var  ExtensionInterface[] */
    protected $extensions;

    protected function __construct(array $config)
    {
        $this->config = $config;
        $this->addCoreExtensions();
        $this->handleData($config);
    }

    public static function createFromYaml($fileName)
    {
        $config = Yaml::parse($fileName);

        return static::createFromArray($config);
    }

    public static function createFromArray(array $config)
    {
        $configs       = [$config];
        $processor     = new Processor();
        $configuration = new Configuration();

        $data = $processor->processConfiguration(
            $configuration,
            $configs
        );

        return new static($data);
    }

    protected function handleData(array $config)
    {
        foreach ($config['tables'] as $name => $tbl) {
            $this->addTable(Table::createFromStructure($name, $this));
        }

        foreach ($this->tables as $table) {
            if ($extensions = $table->getConfig()['extensions']) {
                foreach ($extensions as $name) {
                    if (!isset($this->extensions[$name])) {
                        throw new \Exception("$name 扩展未注册");
                    }

                    $this->extensions[$name]->boot($table);
                }
            }
        }

        foreach ($this->tables as $table) {
            if ($belongToNames = $table->getConfig()['belong_to']) {
                foreach ($belongToNames as $name) {
                    $table->addBelongTo($this->getTables()[$name]);
                }
            }
        }

        foreach ($config['many_many'] as $relation) {
            $table = Relation::createManyToManyTable(
                $this->tables[$relation[0]],
                $this->tables[$relation[1]]
            );

            $this->addTable($table);
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    protected function addTable(Table $table)
    {
        $this->tables[$table->getName()] = $table;

        return $this;
    }

    public function getTables()
    {
        return $this->tables;
    }

    public function registerExtension(ExtensionInterface $extension)
    {
        $this->extensions[$extension->getName()] = $extension;
    }

    protected function addCoreExtensions()
    {
        $this->registerExtension(new TimestampAbleExtension());
        $this->registerExtension(new TreeAbleExtension());
    }
}
