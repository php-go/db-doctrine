<?php
/**
 * User: dongww
 * Date: 14-5-29
 * Time: 下午2:44
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class Structure
{
    /** @var  array */
    protected $data;

    public function __construct(array $structure)
    {
        $this->data = $structure;
    }

    public static function createFromYaml($fileName)
    {
        $configs       = [Yaml::parse($fileName)];
        $processor     = new Processor();
        $configuration = new Configuration();

        $data = $processor->processConfiguration(
            $configuration,
            $configs
        );

        return new self($data);
    }

    /**
     * @return array
     */
    public function getStructure()
    {
        return $this->data;
    }
}
