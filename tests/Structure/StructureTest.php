<?php
/**
 * User: dongww
 * Date: 2014-10-10
 * Time: 18:52
 */

namespace PhpGo\Db\Doctrine\Dbal\Test\Structure;

use PhpGo\Db\Doctrine\Dbal\Structure\Structure;

class StructureTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Structure */
    protected $structure;

    public function setup()
    {
        $this->structure = Structure::createFromYaml(__DIR__ . '/../config/structure.yml');
    }

    public function testConfig()
    {
        $config = $this->structure->getConfig();

        $this->assertArrayHasKey('many_many', $config);
        $this->assertArrayHasKey('tables', $config);
        $this->assertCount(5, $this->structure->getTables());
    }
}
