<?php
/**
 * User: dongww
 * Date: 14-5-29
 * Time: 下午2:44
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class Structure implements ConfigAbleInterface
{
    protected $config;
    /**
     * @var Table[]
     */
    protected $tables;

    protected function __construct(array $config)
    {
        $this->config = $config;
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

    protected function handleData(array $data)
    {
        foreach ($data['tables'] as $name => $tbl) {
            $this->tables[$name] = Table::createFromStructure($name, $this);
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getTables()
    {
        return $this->tables;
    }
}
