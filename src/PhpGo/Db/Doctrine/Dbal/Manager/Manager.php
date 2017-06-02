<?php
/**
 * User: dongww
 * Date: 14-5-27
 * Time: 下午2:32
 */

namespace PhpGo\Db\Doctrine\Dbal\Manager;

use PhpGo\Db\Doctrine\Dbal\Event\DbEvents;
use PhpGo\Db\Doctrine\Dbal\Event\StoreEvent;
use PhpGo\Db\Doctrine\Dbal\Structure\Relation\Relation;
use PhpGo\Db\Doctrine\Dbal\Structure\Table;

class Manager
{
    /** @var  Table */
    protected $table;

    /** @var  ManagerFactory */
    protected $mf;

    protected $one2Many = [];
    protected $many2One = [];
    protected $many2Many = [];

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->mf->getConnection();
    }

    public function __construct(ManagerFactory $mf, Table $table)
    {
        $this->setTable($table);
        $this->mf = $mf;
    }

    /**
     * @param  Table $table
     *
     * @return Manager
     */
    protected function setTable(Table $table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * 获取表结构对象
     *
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }

    public function getTableName()
    {
        return $this->table->getName();
    }

    public function getManagerFactory()
    {
        return $this->mf;
    }

    /**
     * 创建一个没有数据的 Entity
     *
     * @param  array $data
     *
     * @return Entity
     */
    public function createEntity(array $data = [])
    {
        $entity = new Entity($this);
        if($data) {
            $fields = $this->table->getFields();

            foreach ($fields as $name => $field) {
                $data[$name] = $field->convertData(
                    isset($data[$name]) ? $data[$name] : null
                );
            }

            $entity->import($data);
        }

        return $entity;
    }

    /**
     * 保存一个 Entity
     *
     * @param  Entity $entity
     *
     * @return int
     */
    public function store(Entity $entity)
    {
        $table = $this->getTable();
        $storeEntity = $this->createEntity();
        $types = [];

        foreach ($table->getFields() as $name => $field) {
            $storeEntity->$name = $field->convertData($entity->get($name));
            $types[] = $field->getRealType();
        }

        $event = new StoreEvent($storeEntity);
        $dispatcher = $this->getManagerFactory()->getDispatcher();

        if($entity->get('id')) {
            $dispatcher->dispatch(DbEvents::STORE_UPDATE, $event);

            return $this->getConnection()->update(
                $this->getTableName(),
                $this->quoteIdentifier($storeEntity->toArray()),
                ['id' => $entity->get('id')],
                $types
            );
        } else {
            $dispatcher->dispatch(DbEvents::STORE_INSERT, $event);

            return $this->getConnection()->insert(
                $this->getTableName(),
                $this->quoteIdentifier($storeEntity->toArray()),
                $types
            );
        }
    }

    protected function quoteIdentifier(array $data)
    {
        $quotedData = [];
        foreach ($data as $key => $value) {
            $key = $this->getConnection()->quoteIdentifier($key);

            $quotedData[$key] = $value;
        }

        return $quotedData;
    }

    /**
     * 从数据库删除指定 Entity 相对应的数据行
     *
     * @param  Entity $entity
     *
     * @return int
     */
    public function remove(Entity $entity)
    {
        return $this->getConnection()->delete(
            $this->getTableName(),
            ['id' => $entity->get('id')]
        );
    }

    /**
     * @param $type
     * @param $oldValue
     *
     * @return bool|\DateTime|float|int|null|string
     */
//    public function cleanData($type, $oldValue)
//    {
//        $value = null;
//        switch ($type) {
//            case Structure::TYPE_DATE:
//            case Structure::TYPE_DATETIME:
//            case Structure::TYPE_TIME:
//                $value = new \DateTime($oldValue);
//                break;
//            case Structure::TYPE_INTEGER:
//                $value = (int)trim($oldValue);
//                break;
//            case Structure::TYPE_FLOAT:
//                $value = (float)trim($oldValue);
//                break;
//            case Structure::TYPE_BOOLEAN:
//                $value = (bool)$oldValue;
//                break;
//            case Structure::TYPE_STRING:
//            case Structure::TYPE_TEXT:
//            default:
//                $value = (string)$oldValue;
//        }
//
//        return $value;
//    }

    /**
     * @param  null $tableName
     *
     * @return string
     */
    protected function allFields($tableName = null)
    {
        if($tableName == null) {
            $tableName = $this->aliases();
        }

        return $tableName . '.*';
    }

    protected function aliases($tableName = null)
    {
        if($tableName == null) {
            return $this->getTableName();
        }

        return $tableName;
    }

    protected function idField($tableName = null)
    {
        return $this->aliases($tableName) . '.id';
    }

//    public static function foreignKey($foreignTable)
//    {
//        return $foreignTable . '_id';
//    }

    /**
     * @return \PhpGo\Db\Doctrine\Dbal\Query\Reader
     */
    public function getReader()
    {
        return $this->getManagerFactory()->getReader();
    }

    /**
     * 根据 Id 获取一个 Entity
     *
     * @param $id
     *
     * @return Entity
     * @throws \Exception
     */
    public function get($id)
    {
        if((int)$id < 1) {
            throw new \Exception('ID必须大于0！');
        }

        $qb = $this->getSelectQueryBuilder()
            ->select($this->allFields())
            ->where($this->idField() . ' = ?')
            ->setMaxResults(1)
            ->setParameter(0, $id);

        $data = $this->getConnection()->fetchAssoc($qb->getSQL(), $qb->getParameters());

        if($data === false) {
            return null;
        }

        $entity = $this->createEntity($data);

        return $entity;
    }

    /**
     * @return array
     */
//    public function getOne2Many()
//    {
//        $structure = $this->getStructure()->getStructure();
//
//        if (empty($this->one2Many)) {
//            foreach ($structure['tables'] as $tblName => $table) {
//                if (in_array($this->getTableName(), (array)$table['belong_to'])) {
//                    $this->one2Many[] = $tblName;
//                }
//            }
//        }
//
//        return $this->one2Many;
//    }

    /**
     * @return array
     */
//    public function getMany2Many()
//    {
//        $structure = $this->getStructure();
//
//        if (empty($this->many2Many)) {
//            foreach ($structure['many_many'] as $mm) {
//                if (in_array($this->getTableName(), $mm)) {
//                    $this->many2Many[] = ($mm[0] == $this->getTableName()) ? $mm[1] : $mm[0];
//                }
//            }
//        }
//
//        return $this->many2Many;
//    }

    /**
     * @return array
     */
//    public function getMany2One()
//    {
//        $structure = $this->getStructure();
//
//        if (empty($this->many2One)) {
//            $this->many2One = $structure['tables'][$this->getTableName()]['belong_to'];
//        }
//
//        return $this->many2One;
//    }

    public function getStructure()
    {
        return $this->mf->getStructure();
    }

    /**
     * @param  array $join
     * @param  array $where
     *
     * @return EntityCollection
     */
    public function select(array $join = [], array $where = [])
    {
        $query = $this->getReader();
        $data = $query->select(
            $this->getTableName(),
            $join,
            '*',
            $where
        );

        $entities = [];

        foreach ($data as $row) {
            $entities[] = $this->createEntity($row);
        }

        return new EntityCollection($entities);
    }

    /**
     * @param  array $join
     * @param  array $where
     *
     * @return bool
     */
    public function has(array $join = [], array $where = [])
    {
        $query = $this->getReader();
        $data = $query->has($this->getTableName(), $join, $where);

        return $data;
    }

    /**
     * @param  array $join
     * @param  array $where
     *
     * @return int
     */
    public function count(array $join = [], array $where = [])
    {
        $query = $this->getReader();
        $data = $query->count($this->getTableName(), $join, '*', $where);

        return $data;
    }

    /**
     * @param  int   $page
     * @param  int   $limit
     * @param  array $where
     * @param  array $join
     *
     * @return array
     */
    public function getPaging($page = 1, $limit = 10, array $where = [], array $join = [])
    {
        $whereArr = [
            'LIMIT' => [($page - 1) * $limit, $limit],
        ];

        $whereArr = array_merge($whereArr, $where);

        $entities = $this->select($join, $whereArr);
        $count = $this->count($join, $where);
        $pages = ceil($count / $limit);

        return [
            'data'     => $entities,
            'count'    => $count,
            'pages'    => $pages,
            'is_first' => $page <= 1,
            'is_last'  => $page >= $pages,
        ];
    }

    /**
     * @param $sql
     *
     * @return Entity[]
     */
    public function query($sql)
    {
        $conn = $this->getConnection();
        $stmt = $conn->query($sql);

        $entities = [];

        foreach ($stmt->fetchAll() as $row) {
            $entities[] = $this->createEntity($row);
        }

        return $entities;
    }

    protected function getSelectQueryBuilder()
    {
        return $this->getConnection()
            ->createQueryBuilder()
            ->from($this->getTableName(), $this->aliases());
    }

    protected function getUpdateQueryBuilder()
    {
        return $this->getConnection()
            ->createQueryBuilder()
            ->update($this->getTableName(), $this->aliases());
    }

    /**
     * @param               $id
     * @param               $relationTableName
     * @param  array        $where
     * @param  array        $options
     *
     * @return EntityCollection
     */
    public function getMany($id, $relationTableName, array $where = [], array $options = [])
    {
        if($this->table->hasOneToManyTable($relationTableName)) {
            $where[Relation::getForeignKey($this->getTableName())] = $id;

            if($options) {
                $where = array_merge(
                    ['AND' => $where],
                    $options
                );
            }

            return $this->getManagerFactory()
                ->getManager($relationTableName)
                ->select(
                    [],
                    $where
                );
        } elseif($this->table->hasManyToManyTable($relationTableName)) {
            $mm = Relation::getManyToManyTableName($this->getTableName(), $relationTableName);
            $manager = $this->getManagerFactory()
                ->getManager($relationTableName);

            $foreignKey = $mm . '.' . Relation::getForeignKey($relationTableName);
            $thisForeignKey = $mm . '.' . Relation::getForeignKey($this->getTableName());

            $where[$thisForeignKey] = $id;

            return $manager->select(
                [
                    '[<]' . $mm => [
                        $relationTableName . '.id' => $foreignKey,
                    ],
                ],
                ['AND' => $where]
            );
        } else {
            return new EntityCollection();
        }
    }

    /**
     * 返回 $key => $value 形式的数组
     *
     * @param  string $v
     * @param  string $k
     *
     * @return array
     */
    public function asMap($v, $k = 'id')
    {
        $data = $this->select();
        $return = [];
        foreach ($data as $d) {
            $return[$d->$k] = $d->$v;
        }

        return $return;
    }

    /**
     * 获取一部分数据
     *
     * @param  string $order
     * @param  int    $limit
     * @param  array  $where
     * @param  array  $join
     *
     * @return EntityCollection
     */
    public function getLimit($order, $limit = 10, array $where = [], array $join = [])
    {
        $whereArray = [
            'ORDER' => $order,
            'LIMIT' => $limit,
        ];

        $where = array_merge($whereArray, $where);

        return $this->select($join, $where);
    }
}
