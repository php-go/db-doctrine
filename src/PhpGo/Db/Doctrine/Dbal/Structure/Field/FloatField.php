<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 15:34
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;


use PhpGo\Db\Doctrine\Dbal\Structure\Table;

class FloatField extends FieldAbstract
{
    public function getType()
    {
        return FieldInterface::TYPE_FLOAT;
    }
}
