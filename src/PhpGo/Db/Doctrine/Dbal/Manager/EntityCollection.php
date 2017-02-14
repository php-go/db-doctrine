<?php
/**
 * User: dongww
 * Date: 2017/2/13
 * Time: 16:53
 */

namespace PhpGo\Db\Doctrine\Dbal\Manager;

class EntityCollection extends \ArrayIterator
{
    public function toArray()
    {
        $arr = [];

        foreach ($this as $entity) {
            $arr[] = $entity->toArray();
        }

        return $arr;
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }
}