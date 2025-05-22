<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller;

use Doctrine\DBAL\Connection;
use MembersBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class HealthCheckController extends AbstractController
{
    use TargetPathTrait;

    #[Route(path: '/health-check', name: 'health_check', methods: [Request::METHOD_GET])]
    public function healthCheck(Connection $connection, KernelInterface $kernel, LoggerInterface $logger, AdapterInterface $cache): JsonResponse
    {
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
            $item = $cache->getItem('health_check');

            $item->set('ok');
            $cache->save($item);

            $fetched = $cache->getItem('health_check');

            if ($fetched->isHit() && $fetched->get() === 'ok') {
                $cache->deleteItem('health_check'); // clean up
                $details['cache'] = 'connected';
            } else {
                $status = 'ERROR! Cache unavailable';
                $details['cache'] = 'unavailable';

                $logger->error(message: 'REDIS NOT CONNECTED!');
            }
        } catch (\Throwable $e) {
            $status = 'ERROR! Cache unavailable';
            $details['cache'] = 'unavailable';

            $logger->error(message: 'REDIS NOT CONNECTED!');
        }

        // Basic system info
        $details['php_version'] = PHP_VERSION;
        $details['pimcore_env'] = $kernel->getEnvironment();

        return new JsonResponse([
            'status' => $status,
            'details' => $kernel->getEnvironment() === 'dev' ? $details : 'ok',
        ], $status === 'ok' ? 200 : 503);
    }
}
