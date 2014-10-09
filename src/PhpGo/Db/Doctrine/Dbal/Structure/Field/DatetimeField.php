<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 15:34
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

class DatetimeField extends FieldAbstract
{
    public function getType()
    {
        return FieldInterface::TYPE_DATETIME;
    }

    public function getRealType()
    {
        return 'datetime';
    }

    public function convertData($data)
    {
        return new \DateTime($data);
    }
}
