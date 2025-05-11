<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller;

use Doctrine\DBAL\Connection;
use MembersBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class HealthCheckController extends AbstractController
{
    use TargetPathTrait;

    #[Route(path: '/health-check', name: 'health_check', methods: [Request::METHOD_GET])]
    public function healthCheck(Connection $connection, CacheInterface $cache, KernelInterface $kernel): JsonResponse
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
        }

        // Check cache connection
        try {
            $cache->get('health-check-key', function (ItemInterface $item) {
                $item->expiresAfter(5);

                return 'ok';
            });
            $details['cache'] = 'connected';
        } catch (\Throwable $e) {
            $status = 'ERROR! Cache unavailable';
            $details['cache'] = 'unavailable';
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
