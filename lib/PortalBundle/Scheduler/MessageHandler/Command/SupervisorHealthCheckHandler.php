<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Scheduler\MessageHandler\Command;

use Doctrine\DBAL\Connection;
use Froq\PortalBundle\Scheduler\Message\SupervisorHealthCheckMessage;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'supervisor_health_check', handles: SupervisorHealthCheckMessage::class, method: '__invoke', priority: 10)]
final class SupervisorHealthCheckHandler
{
    public function __construct(
        private readonly Connection $connection,
        private readonly LoggerInterface $logger,
        private readonly AdapterInterface $cache,
        private readonly string $redisUrl
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(SupervisorHealthCheckMessage $supervisorHealthCheckMessage): void
    {
        $status = 'ok';
        $details = [];

        // Check DB
        try {
            $this->connection->connect();
        } catch (\Throwable $exception) {
            $status = 'ERROR! DB connection failed!';
            $details[] = 'database connection_failed';

            $this->logger->error(message: "DB NOT CONNECTED! {$supervisorHealthCheckMessage->name}");
        }

        // Check Redis
        try {
            $redis = new Client($this->redisUrl);
            $redis->set('test-key', 'ok');

            $item = $this->cache->getItem('health_check');

            $item->set('ok');
            $this->cache->save($item);

            $fetched = $this->cache->getItem('health_check');

            if ($fetched->isHit() && $fetched->get() === 'ok' && $redis->get('test-key') === 'ok') {
                $this->cache->deleteItem('health_check'); // clean up
                $details['cache'] = 'connected';
            }
        } catch (\Throwable $exception) {
            $status = 'ERROR! Cache unavailable';
            $details[] = 'cache unavailable';

            $this->logger->error(message: "REDIS NOT CONNECTED! {$supervisorHealthCheckMessage->name}");
        }

        $this->logger->info($status === 'ok' ? "{$supervisorHealthCheckMessage->name} ok!" : implode(',', $details));
    }
}
