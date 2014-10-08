<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 15:34
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

class ArrayField extends FieldAbstract
{
    public function getType()
    {
        return FieldInterface::TYPE_ARRAY;
    }

    public function getRealType()
    {
        return 'array';
    }
}
