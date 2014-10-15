<?php
/**
 * User: dongww
 * Date: 2014-10-10
 * Time: 15:10
 */

namespace PhpGo\Db\Doctrine\Dbal\Extension\EventListener;

use PhpGo\Db\Doctrine\Dbal\Event\DbEvents;
use PhpGo\Db\Doctrine\Dbal\Event\StoreEvent;
use PhpGo\Db\Doctrine\Dbal\Extension\TimestampAbleExtension;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TimestampAbleListener implements EventSubscriberInterface
{
    public function onInsert(StoreEvent $event)
    {
        $bean   = $event->getBean();
        $config = $bean->getTable()->getConfig();

        if (in_array(TimestampAbleExtension::NAME, $config['extensions'])) {
            $bean->created_at = $bean->updated_at = new \DateTime();
        }
    }

    public function onUpdate(StoreEvent $event)
    {
        $bean   = $event->getBean();
        $config = $bean->getTable()->getConfig();

        if (in_array(TimestampAbleExtension::NAME, $config['extensions'])) {
            $bean->updated_at = new \DateTime();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            DbEvents::STORE_INSERT => [
                ['onInsert', 0],
            ],
            DbEvents::STORE_UPDATE => [
                ['onUpdate', 0],
            ],
        ];
    }
}
