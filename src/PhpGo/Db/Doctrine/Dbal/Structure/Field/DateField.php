<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 15:34
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

class DateField extends FieldAbstract
{
    public function getType()
    {
        return FieldInterface::TYPE_DATE;
    }

    public function getRealType()
    {
        return 'date';
    }

    public function convertData($data)
    {
        return new \DateTime($data);
    }
}
