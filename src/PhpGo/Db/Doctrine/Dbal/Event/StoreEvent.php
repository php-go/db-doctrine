<?php
/**
 * User: dongww
 * Date: 2014-10-10
 * Time: 14:40
 */

namespace PhpGo\Db\Doctrine\Dbal\Event;

use PhpGo\Db\Doctrine\Dbal\Manager\Entity;
use Symfony\Component\EventDispatcher\Event;

class StoreEvent extends Event
{
    /** @var  Entity */
    protected $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity()
    {
        return $this->entity;
    }
}
