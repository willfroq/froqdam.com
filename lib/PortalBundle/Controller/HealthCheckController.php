<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller;

use Doctrine\DBAL\Connection;
use Froq\PortalBundle\Scheduler\Message\SupervisorHealthCheckMessage;
use MembersBundle\Controller\AbstractController;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class HealthCheckController extends AbstractController
{
    #[Route(path: '/health-check', name: 'health_check', methods: [Request::METHOD_GET])]
    public function healthCheck(
        Connection $connection,
        KernelInterface $kernel,
        LoggerInterface $logger,
        AdapterInterface $cache,
        string $redisUrl,
        MessageBusInterface $messageBus
    ): JsonResponse {
        $status = 'ok';
        $details = [];

        // Check DB connection
        try {
            $connection->connect();
            $details['database'] = 'connected';
        } catch (\Throwable $e) {
            $status = 'ERROR! DB connection failed!';
            $details['database'] = 'connection_failed';

            $logger->error(message: 'DB NOT CONNECTED!');
        }

        // Check cache connection
        try {
            $redis = new Client($redisUrl);
            $redis->set('test-key', 'ok');

            $item = $cache->getItem('health_check');

            $item->set('ok');
            $cache->save($item);

            $fetched = $cache->getItem('health_check');

            if ($fetched->isHit() && $fetched->get() === 'ok' && $redis->get('test-key') === 'ok') {
                $cache->deleteItem('health_check'); // clean up
                $details['cache'] = 'connected';
            }
        } catch (\Throwable $e) {
            $status = 'ERROR! Cache unavailable';
            $details['cache'] = 'unavailable';

            $logger->error(message: 'REDIS NOT CONNECTED!');
        }

        $messageBus->dispatch(new SupervisorHealthCheckMessage('supervisor_health_check'));

        // Basic system info
        $details['php_version'] = PHP_VERSION;
        $details['pimcore_env'] = $kernel->getEnvironment();

        return new JsonResponse([
            'status' => $status,
            'details' => $kernel->getEnvironment() === 'dev' ? $details : 'ok',
        ], $status === 'ok' ? 200 : 503);
    }
}
