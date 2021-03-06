<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 15:34
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

class DateField extends DatetimeField
{
    public function getType()
    {
        return FieldInterface::TYPE_DATE;
    }

    public function getRealType()
    {
        return 'date';
    }
}
