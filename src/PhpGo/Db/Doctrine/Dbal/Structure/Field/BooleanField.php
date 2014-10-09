<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 15:34
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

class BooleanField extends FieldAbstract
{
    public function getType()
    {
        return FieldInterface::TYPE_BOOLEAN;
    }

    public function getRealType()
    {
        return 'boolean';
    }

    public function convertData($data)
    {
        return (bool)$data;
    }
}
