<?php

declare(strict_types=1);

namespace Froq\AssetBundle\EventSubscriber;

use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class DoctrineLockWaitTimeoutSubscriber implements EventSubscriberInterface
{
    /** @return string[] */
    public static function getSubscribedEvents(): array
    {
        return [Events::postConnect];
    }

    /**
     * @throws Exception
     */
    public function postConnect(ConnectionEventArgs $args): void
    {
        $args->getConnection()->executeQuery('SET innodb_lock_wait_timeout = 5');
    }
}
