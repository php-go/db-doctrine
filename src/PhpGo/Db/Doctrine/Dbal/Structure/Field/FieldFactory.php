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
                $class = __NAMESPACE__ . '\ArrayField';
                break;
            case FieldInterface::TYPE_BOOLEAN :
                $class = __NAMESPACE__ . '\BooleanField';
                break;
            case FieldInterface::TYPE_DATE :
                $class = __NAMESPACE__ . '\DateField';
                break;
            case FieldInterface::TYPE_DATETIME :
                $class = __NAMESPACE__ . '\DatetimeField';
                break;
            case FieldInterface::TYPE_FLOAT :
                $class = __NAMESPACE__ . '\FloatField';
                break;
            case FieldInterface::TYPE_INTEGER :
                $class = __NAMESPACE__ . '\IntegerField';
                break;
            case FieldInterface::TYPE_STRING :
                $class = __NAMESPACE__ . '\StringField';
                break;
            case FieldInterface::TYPE_TEXT :
                $class = __NAMESPACE__ . '\TextField';
                break;
            case FieldInterface::TYPE_TIME :
                $class = __NAMESPACE__ . '\TimeField';
                break;
            default:
                return null;
        }

        return new $class($name, $table, $config['required'], $config['index'], $config['unique']);
    }
}
