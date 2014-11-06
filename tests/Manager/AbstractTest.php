<?php
/**
 * User: dongww
 * Date: 2014-10-10
 * Time: 19:08
 */

namespace PhpGo\Db\Doctrine\Dbal\Test\Manager;


use Doctrine\DBAL\Connection;
use PhpGo\Db\Doctrine\Dbal\Manager\ManagerFactory;
use PhpGo\Db\Doctrine\Dbal\Structure\Structure;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;

abstract class AbstractTest extends \PHPUnit_Extensions_Database_TestCase
{
    /** @var Connection $conn */
    protected $conn;

    /** @var  ManagerFactory */
    protected $mf;

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        $pdo = null;

        if (!$this->conn) {
            $this->conn = require __DIR__ . '/../config/config.php';
            $structure = Structure::createFromYaml(__DIR__ . '/../config/structure.yml');
            $this->mf  = new ManagerFactory($this->conn, $structure);
            $pdo = $this->conn->getWrappedConnection();
            $pdo->query("SET foreign_key_checks = 0");
        }

        return $this->createDefaultDBConnection($pdo);
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(
            __DIR__ . '/../config/test_structure.yml'
        );
    }

    public function testManagerFactory()
    {
        $this->assertInstanceOf('Doctrine\DBAL\Connection', $this->conn);
        $this->assertInstanceOf('PhpGo\Db\Doctrine\Dbal\Manager\ManagerFactory',$this->mf);
    }
}
