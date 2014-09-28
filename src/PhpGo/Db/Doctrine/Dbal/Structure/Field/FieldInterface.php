<?php
/**
 * User: dongww
 * Date: 2014-9-25
 * Time: 20:20
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

interface FieldInterface
{
    const TYPE_STRING = 'string';
    const TYPE_TEXT = 'text';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_DATETIME = 'datetime';
    const TYPE_DATE = 'date';
    const TYPE_TIME = 'time';
    const TYPE_ARRAY = 'array';

    public function getType();
}
