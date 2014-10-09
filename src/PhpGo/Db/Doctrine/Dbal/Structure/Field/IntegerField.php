<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 15:34
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

class IntegerField extends FieldAbstract
{
    public function getType()
    {
        return FieldInterface::TYPE_INTEGER;
    }

    public function getRealType()
    {
        return 'integer';
    }

    public function convertData($data)
    {
        return (int)$data;
    }
}
