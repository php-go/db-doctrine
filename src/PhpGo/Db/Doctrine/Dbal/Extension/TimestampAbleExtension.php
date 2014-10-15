<?php
/**
 * User: dongww
 * Date: 2014-10-7
 * Time: 14:38
 */

namespace PhpGo\Db\Doctrine\Dbal\Extension;

use PhpGo\Db\Doctrine\Dbal\Extension\EventListener\TimestampAbleListener;
use PhpGo\Db\Doctrine\Dbal\Manager\ManagerFactory;
use PhpGo\Db\Doctrine\Dbal\Structure\Field\DatetimeField;
use PhpGo\Db\Doctrine\Dbal\Structure\Table;

class TimestampAbleExtension implements ExtensionInterface
{
    const NAME = 'timestamp_able';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';

    public function getName()
    {
        return static::NAME;
    }

    public function extendTable(Table $table)
    {
        $table->addField(new DatetimeField(static::FIELD_CREATED_AT, $table));
        $table->addField(new DatetimeField(static::FIELD_UPDATED_AT, $table));
    }

    public function registerListener(ManagerFactory $mf)
    {
        $listener = new TimestampAbleListener();
        $mf->getDispatcher()->addSubscriber($listener);
    }

    public function registerManager(ManagerFactory $mf)
    {
    }
}
