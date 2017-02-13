<?php
/**
 * User: dongww
 * Date: 2017/2/13
 * Time: 16:53
 */

namespace PhpGo\Db\Doctrine\Dbal\Manager;

class BeanCollection extends \ArrayIterator
{
    public function toArray()
    {
        $arr = [];

        foreach ($this as $bean) {
            $arr[] = $bean->toArray();
        }

        return $arr;
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }
}