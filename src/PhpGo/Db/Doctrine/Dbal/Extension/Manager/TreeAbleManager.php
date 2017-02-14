<?php
/**
 * User: dongww
 * Date: 14-7-15
 * Time: 上午10:01
 */

namespace PhpGo\Db\Doctrine\Dbal\Extension\Manager;

use Doctrine\DBAL\Connection;
use PhpGo\Db\Doctrine\Dbal\Manager\Entity;
use PhpGo\Db\Doctrine\Dbal\Manager\Manager;

class TreeAbleManager extends Manager
{
    const INSERT_PREVIOUS = 1;
    const INSERT_NEXT     = 2;

    protected $categories;

    public function addChildNode(Entity $entity, Entity $parentEntity = null)
    {
        $qb = $this->getSelectQueryBuilder()
            ->select('max(sort)');

        if ($parentEntity != null) {
            $qb
                ->where('parent_id = ?')
                ->setParameter(0, $parentEntity->id);
        } else {
            $qb
                ->where('parent_id is null');
        }

        $maxSort         = $this->getConnection()->fetchColumn($qb->getSQL(), $qb->getParameters());
        $entity->sort      = $maxSort + 1;
        $entity->path      = $this->getChildPath($parentEntity);
        $entity->level     = $this->getChildLevel($parentEntity);
        $entity->parent_id = $parentEntity ? $parentEntity->id : null;

        return $this->store($entity);
    }

    public function addPreNode(Entity $insertEntity, Entity $currentEntity)
    {
        return $this->insertNode($insertEntity, $currentEntity, self::INSERT_PREVIOUS);
    }

    public function addNextNode(Entity $insertEntity, Entity $currentEntity)
    {
        return $this->insertNode($insertEntity, $currentEntity, self::INSERT_NEXT);
    }

    public function insertNode(Entity $insertEntity, Entity $currentEntity, $position = self::INSERT_NEXT)
    {
        $parentId   = $currentEntity->parent_id;
        $sort       = $currentEntity->sort;
        $parentEntity = $currentEntity->parent;

        $qb = $this->getUpdateQueryBuilder()
            ->set('sort', 'sort + 1');

        if ($position == self::INSERT_PREVIOUS) {
            $qb->where('sort >= :sort');
            $insertEntity->sort = $sort;
        } else {
            $qb->where('sort > :sort');
            $insertEntity->sort = $sort + 1;
        }

        $qb->setParameter('sort', $sort);

        if ($parentId) {
            $qb
                ->andWhere('parent_id = :pid')
                ->setParameter('pid', $parentId);
        } else {
            $qb->andWhere('parent_id is null');
        }

        $this->getConnection()->executeUpdate($qb->getSQL(), $qb->getParameters());

        $insertEntity->parent_id = $parentId;
        $insertEntity->path      = $this->getChildPath($parentEntity);
        $insertEntity->level     = $this->getChildLevel($parentEntity);

        return $this->store($insertEntity);
    }

    public function move(Entity $entity, Entity $newParentEntity = null, $newSort = 1)
    {
        $oldParentId = $entity->parent_id;
        $oldSort     = $entity->sort;

        //原来的同级排序进行压缩
        $qb = $this->getUpdateQueryBuilder()
            ->set('sort', 'sort - 1')
            ->where('sort > :sort')
            ->setParameter('sort', $oldSort);

        if ($oldParentId) {
            $qb
                ->andWhere('parent_id = :pid')
                ->setParameter('pid', $oldParentId);
        } else {
            $qb->andWhere('parent_id is null');
        }

        $this->getConnection()->executeUpdate($qb->getSQL(), $qb->getParameters());

        //新的统计排序给出空挡
        $qb = $this->getUpdateQueryBuilder()
            ->set('sort', 'sort + 1')
            ->where('sort >= :sort')
            ->setParameter('sort', $newSort);

        if ($newParentEntity) {
            $qb
                ->andWhere('parent_id = :pid')
                ->setParameter('pid', $newParentEntity->id);
        } else {
            $qb->andWhere('parent_id is null');
        }

        $this->getConnection()->executeUpdate($qb->getSQL(), $qb->getParameters());

        $replacePathFrom  = $entity->path . $entity->id . '/';
        $replaceLevelFrom = $entity->level;

        //更新移动节点的path、sort、level
        $entity->parent_id = $newParentEntity ? $newParentEntity->id : null;
        $entity->sort      = $newSort;
        $entity->path      = $this->getChildPath($newParentEntity);
        $entity->level     = $this->getChildLevel($newParentEntity);

        //更新所有子节点的路径
        if ($this->store($entity)) { //echo 1;exit;
            $replacePathTo  = $entity->path . $entity->id . '/';
            $replaceLevelTo = $entity->level;

            $qb = $this->getUpdateQueryBuilder()
                ->set('path', 'REPLACE(path, :replace_path_from, :replace_path_to)')
                ->set('level', 'level + ( :replace_level_to - :replace_level_from )')
                ->where('path like :like')
                ->setParameter('replace_path_from', $replacePathFrom)
                ->setParameter('replace_path_to', $replacePathTo)
                ->setParameter('replace_level_to', $replaceLevelTo)
                ->setParameter('replace_level_from', $replaceLevelFrom)
                ->setParameter('like', $replacePathFrom . '%');

            $this->getConnection()->executeUpdate($qb->getSQL(), $qb->getParameters());

            return true;
        }

        return false;
    }

    public function remove(Entity $entity)
    {
        //todo 可能有点问题，需要进一步测试
        $qb = $this->getSelectQueryBuilder()
            ->select('id')
            ->where('parent_id = :pid')
            ->setParameter('pid', $entity->id);

        $data = $this->getConnection()->fetchAll($qb->getSQL(), $qb->getParameters());

        foreach ($data as $d) {
            $childEntity = $this->get($d['id']);
            $this->move($childEntity, null, 1);
        }

        $qb = $this->getUpdateQueryBuilder()
            ->set('sort', 'sort - 1')
            ->where('sort > :sort')
            ->setParameter('sort', $entity->sort);

        if ($entity->parent_id) {
            $qb
                ->andWhere('parent_id = :pid')
                ->setParameter('pid', $entity->parent_id);
        } else {
            $qb->andWhere('parent_id is null');
        }

        $this->getConnection()->executeUpdate($qb->getSQL(), $qb->getParameters());

        return parent::remove($entity);
    }

    protected function getChildPath(Entity $entity = null)
    {
        $path = null;

        if ($entity) {
            $basePath = $entity->path ?: '/';
            $path     = $basePath . $entity->id . '/';
        } else {
            $path = null;
        }

        return $path;
    }

    protected function getChildLevel(Entity $entity = null)
    {
        $level = null;

        if ($entity) {
            $level = $entity->level + 1;
        } else {
            $level = 1;
        }

        return $level;
    }

    protected function reloadCategory()
    {
        $qb = $this->getSelectQueryBuilder()
            ->select($this->allFields())
            ->orderBy('sort');

        return $this->categories = $this->getConnection()->fetchAll($qb->getSQL());
    }

    protected function hasChildren($id)
    {
        foreach ($this->categories as $row) {
            if ($row['parent_id'] == $id) {
                return true;
            }
        }

        return false;
    }

    public function getTreeView($parent = 0)
    {
        if (!$this->categories) {
            $this->reloadCategory();
        }

        $result = '<ul>';
        if ($this->categories) {
            foreach ($this->categories as $row) {
                if ($row['parent_id'] == $parent) {
                    $result .= '<li class="jstree-open" id="category_' .
                        $this->getTableName() . '_' . $row['id'] . '">' . $row['title'];
                    if ($this->hasChildren($row['id'])) {
                        $result .= $this->getTreeView($row['id']);
                    }

                    $result .= "</li>";
                }
            }
        }

        $result .= "</ul>";

        return $result;
    }

    /**
     * 获得排序后的列表
     *
     * @param  int          $pid
     * @return array|string
     */
    protected function getSorted($pid = null)
    {
        if (!$this->categories) {
            $this->reloadCategory();
        }
        $result = [];
        if ($this->categories) {
            foreach ($this->categories as $row) {
                if ($row['parent_id'] == $pid) {
                    $result[] = $row;
                    if ($this->hasChildren($row['id'])) {
                        $result = array_merge($result, (array) $this->getSorted($row['id']));
                    }
                }
            }
        }

        return $result;
    }

    public function getCateMap($pre = '--')
    {
        $data   = $this->getSorted();
        $return = [];
        foreach ($data as $d) {
            $str              = str_repeat($pre, $d['level'] - 1);
            $return[$d['id']] = $str . $d['title'];
        }

        return $return;
    }

    /**
     * 获取子节点的id集合
     *
     * @param  int   $pid
     * @param  bool  $topLevel
     * @param  bool  $includeSelf
     * @return array
     */
    public function getChildrenIds($pid = 0, $topLevel = false, $includeSelf = true)
    {
        $id  = (int) $pid;
        $ids = [];

        if ($id < 1) {
            $qb = $this->getSelectQueryBuilder()->select('id');

            $data = $this->getConnection()->fetchAll($qb->getSQL());
            foreach ($data as $d) {
                $ids[] = (int) $d['id'];
            }

            return $ids;
        }

        if ($includeSelf) {
            $ids[] = $id;
        }

        if ($topLevel) {
            $qb = $this->getSelectQueryBuilder()
                ->select('id')
                ->where('parent_id = :pid')
                ->setParameter('pid', $id);

            $data = $this->getConnection()->fetchAll($qb->getSQL(), $qb->getParameters());
            foreach ($data as $d) {
                $ids[] = (int) $d['id'];
            }
        } else {
            $qb = $this->getSelectQueryBuilder()
                ->select('id')
                ->where('path like :path')
                ->setParameter('path', '%/' . $id . '/%');

            $data = $this->getConnection()->fetchAll($qb->getSQL(), $qb->getParameters());
            foreach ($data as $d) {
                $ids[] = (int) $d['id'];
            }
        }

        return $ids;
    }

    /**
     * 获得子节点的 Entity 集合
     *
     * @param  int    $pid
     * @param  bool   $topLevel
     * @param  bool   $includeSelf
     *
*@return Entity[]
     */
    public function getChildren($pid = 0, $topLevel = false, $includeSelf = true)
    {
        $ids   = $this->getChildrenIds($pid, $topLevel, $includeSelf);
        $entities = [];

        $qb = $this->getSelectQueryBuilder()
            ->select('*')
            ->where('id in (?)')
            ->orderBy('sort');

        $data = $this->getConnection()->fetchAll(
            $qb->getSQL(),
            array($ids),
            array(Connection::PARAM_INT_ARRAY)
        );

        foreach ($data as $d) {
            $entities[] = $this->createEntity($d);
        }

        return $entities;
    }
    //todo 新增编辑移动节点时，以数组形式将路径信息保存到数据库，以便读取。 格式：[['id'=>1, 'title'=>'节点名称'], [...]]
}
