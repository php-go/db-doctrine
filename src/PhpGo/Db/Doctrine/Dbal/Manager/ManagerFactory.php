<?php
/**
 * User: dongww
 * Date: 14-5-27
 * Time: 下午3:57
 */

namespace PhpGo\Db\Doctrine\Dbal\Manager;

use PhpGo\Db\Doctrine\Dbal\Query;
use Doctrine\DBAL\Connection;
use PhpGo\Db\Doctrine\Dbal\Structure\Structure;
use Doctrine\DBAL\Logging\DebugStack;
use PhpGo\Db\Doctrine\Dbal\Structure\Table;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ManagerFactory
{
    /** @var  Connection */
    protected $conn;
    /** @var  Structure */
    protected $structure;

    /** @var Manager[] */
    protected $managers = [];

    /** @var  Query\MedooAdapter */
    protected $medoo;

    /** @var  Query\Reader */
    protected $reader;

    protected $debug;

    /** @var DebugStack */
    protected $logger;

    protected $dispatcher;

    public function __construct(Connection $conn, Structure $structure, $debug = false)
    {
        $this->debug = (bool) $debug;
        $this->setConnection($conn);
        $this->setStructure($structure);
        $this->dispatcher = new EventDispatcher();

        foreach ($this->structure->getExtensions() as $extension) {
            $extension->registerListener($this);
            $extension->registerManager($this);
        }
    }

    public function registerManager($tableName, Manager $manager)
    {
        if (!$this->structure->hasTable($tableName)) {
            throw new \Exception("数据表 %$tableName 不存在");
        }

        $this->managers[$tableName] = $manager;
    }

    public function setConnection(Connection $conn)
    {
        $this->conn = $conn;

        if ($this->debug) {
            $this->logger = new DebugStack();
            $this->conn->getConfiguration()->setSQLLogger($this->logger);
        }
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * @return EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function getMedoo()
    {
        if (!($this->medoo instanceof Query\MedooAdapter)) {
            $this->medoo = new Query\MedooAdapter($this->getConnection());
        }

        return $this->medoo;
    }

    public function getReader()
    {
        if (!($this->reader instanceof Query\Reader)) {
            $this->reader = new Query\Reader($this->getMedoo());
        }

        return $this->reader;
    }

    /**
     * @param Structure $structure
     */
    public function setStructure(Structure $structure)
    {
        $this->structure = $structure;
    }

    /**
     * @return Structure
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @param  Table|string $table
     * @return Manager
     * @throws \Exception
     */
    public function getManager($table)
    {
        $structure = $this->getStructure();

        if ($table instanceof Table) {
            $tableName = $table->getName();
        } else {
            $tableName = $table;
        }

        if (!$structure->hasTable($tableName)) {
            throw new \Exception("数据表 %$tableName 不存在");
        }

        if (!isset($this->managers[$tableName])) {
            $this->managers[$tableName] = new Manager(
                $this,
                $this->getStructure()->getTable($tableName)
            );
        }

        return $this->managers[$tableName];
    }

    /**
     * @return array
     */
    public function getSqlStack()
    {
        if (!$this->debug) {
            return [];
        }

        return $this->logger->queries;
    }
}
