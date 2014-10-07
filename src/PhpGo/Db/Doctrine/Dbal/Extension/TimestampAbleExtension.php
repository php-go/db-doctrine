<?php
/**
 * User: dongww
 * Date: 2014-10-7
 * Time: 14:38
 */

namespace PhpGo\Db\Doctrine\Dbal\Extension;

use PhpGo\Db\Doctrine\Dbal\Structure\Field\DatetimeField;
use PhpGo\Db\Doctrine\Dbal\Structure\Table;

class TimestampAbleExtension implements ExtensionInterface
{
    public function getName()
    {
        return 'timestamp_able';
    }

    public function boot(Table $table)
    {
        $table->addField(new DatetimeField('created_at', $table));
        $table->addField(new DatetimeField('updated_at', $table));
    }
}
