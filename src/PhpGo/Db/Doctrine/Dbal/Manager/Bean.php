<?php
/**
 * User: dongww
 * Date: 14-5-27
 * Time: 下午2:14
 */

namespace PhpGo\Db\Doctrine\Dbal\Manager;

use PhpGo\Db\Doctrine\Dbal\Structure\Relation\Relation;
use PhpGo\Db\Doctrine\Dbal\Structure\Table;

class Bean
{
    protected $data = [];

    /** @var  Manager */
    protected $manager;
    /** @var  Table */
    protected $table;

    public function __construct(Manager $manager)
    {
        $this->setManager($manager);
    }

    public function getTable()
    {
        if (empty($this->table)) {
            $this->table = $this->manager->getTable();
        }

        return $this->table;
    }

//    public function getOne2Many()
//    {
//        return $this->getManager()->getOne2Many();
//    }
//
//    public function getMany2Many()
//    {
//        return $this->getManager()->getMany2Many();
//    }
//
//    public function getMany2One()
//    {
//        return $this->getManager()->getMany2One();
//    }

    /**
     * @return array
     */
    protected function getStructure()
    {
        return $this->getManagerFactory()
            ->getStructure();
    }

    /**
     * @return Manager
     */
    public function getManager()
    {
        return $this->manager;
    }

    protected function setManager(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function getManagerFactory()
    {
        return $this->manager->getManagerFactory();
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __isset($name)
    {
//        return isset($this->data[$name]) ? true : false;
        return true;
    }

    public function get($fieldName)
    {
        if (isset($this->data[$fieldName])) {
            return $this->data[$fieldName];
        }

        return $this->getBelongTo($fieldName);
    }

    public function set($fieldName, $value)
    {
        $this->data[$fieldName] = $value;
    }

    public function import(array $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * 获得 BelongTo Bean
     *
     * @param $name
     * @throws \Exception
     * @return Bean|null
     */
    public function getBelongTo($name)
    {
        $foreignKey = Relation::getForeignKey($name);

        if (!isset($this->data[$foreignKey])) {
            return null;
        }

        $foreignTable = $this->table->getBelongToTable($name);

        $m = $this->getManagerFactory()->getManager($foreignTable);

        return $m->get($this->data[$foreignKey]);
    }

    public function getMany($relationTableName, array $where = [], array $options = [])
    {
        return $this->getManager()->getMany($this->id, $relationTableName, $where, $options);
    }
}
