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
        $entity   = $event->getEntity();
        $config = $entity->getTable()->getConfig();

        if (in_array(TimestampAbleExtension::NAME, $config['extensions'])) {
            $entity->created_at = $entity->updated_at = new \DateTime();
        }
    }

    public function onUpdate(StoreEvent $event)
    {
        $entity   = $event->getEntity();
        $config = $entity->getTable()->getConfig();

        if (in_array(TimestampAbleExtension::NAME, $config['extensions'])) {
            $entity->updated_at = new \DateTime();
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
