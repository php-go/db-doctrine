<?php
/**
 * User: dongww
 * Date: 2014-9-26
 * Time: 15:32
 */

namespace PhpGo\Db\Doctrine\Dbal\Structure\Field;

use PhpGo\Db\Doctrine\Dbal\Structure\Table;

class FieldFactory
{
    public static function create($name, Table $table)
    {
        $config = $table->getConfig()['fields'][$name];

        switch ($config['type']) {
            case FieldInterface::TYPE_ARRAY :
                $class = '\ArrayField';
                break;
            case FieldInterface::TYPE_BOOLEAN :
                $class = '\BooleanField';
                break;
            case FieldInterface::TYPE_DATE :
                $class = '\DateField';
                break;
            case FieldInterface::TYPE_DATETIME :
                $class = '\DatetimeField';
                break;
            case FieldInterface::TYPE_FLOAT :
                $class = '\FloatField';
                break;
            case FieldInterface::TYPE_INTEGER :
                $class = '\IntegerField';
                break;
            case FieldInterface::TYPE_STRING :
                $class = '\StringField';
                break;
            case FieldInterface::TYPE_TEXT :
                $class = '\TextField';
                break;
            case FieldInterface::TYPE_TIME :
                $class = '\TimeField';
                break;
            default:
                return null;
        }

        $class = __NAMESPACE__ . $class;

        unset($config['type']);

        return new $class($name, $table, $config);
    }
}
