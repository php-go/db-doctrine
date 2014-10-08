<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 15:34
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

class FloatField extends FieldAbstract
{
    public function getType()
    {
        return FieldInterface::TYPE_FLOAT;
    }

    public function getRealType()
    {
        return 'float';
    }
}
