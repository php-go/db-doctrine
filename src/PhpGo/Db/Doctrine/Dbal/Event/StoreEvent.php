<?php
/**
 * User: dongww
 * Date: 2014-10-10
 * Time: 14:40
 */

namespace PhpGo\Db\Doctrine\Dbal\Event;

use PhpGo\Db\Doctrine\Dbal\Manager\Bean;
use Symfony\Component\EventDispatcher\Event;

class StoreEvent extends Event
{
    /** @var  Bean */
    protected $bean;

    public function __construct(Bean $bean)
    {
        $this->bean = $bean;
    }

    public function getBean()
    {
        return $this->bean;
    }
}
