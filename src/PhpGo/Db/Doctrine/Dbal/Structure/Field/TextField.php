<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 15:34
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

class TextField extends FieldAbstract
{
    public function getType()
    {
        return FieldInterface::TYPE_TEXT;
    }

    public function getRealType()
    {
        return 'text';
    }

    public function convertData($data)
    {
        return (string)$data;
    }
}
