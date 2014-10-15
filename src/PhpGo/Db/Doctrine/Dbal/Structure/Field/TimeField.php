<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 15:34
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

class TimeField extends DatetimeField
{
    public function getType()
    {
        return FieldInterface::TYPE_TIME;
    }

    public function getRealType()
    {
        return 'time';
    }
}
