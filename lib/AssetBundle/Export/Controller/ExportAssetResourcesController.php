<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\Controller;

use Froq\AssetBundle\Export\Action\BuildExportAssetResourceCollection;
use Pimcore\Model\DataObject\Organization;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/asset-resources/{organizationId}', name: 'froq_portal_export.asset_resources', methods: [Request::METHOD_GET])]
final class ExportAssetResourcesController extends AbstractController
{
    /**
     * @throws \Exception
     */
    public function __invoke(Request $request, int $organizationId, BuildExportAssetResourceCollection $buildExportAssetResourceCollection): JsonResponse
    {
        $organization = Organization::getById($organizationId);

        if (!($organization instanceof Organization)) {
            return $this->json(data: ['validationErrors' => 'Invalid Request', 'status' => 422], status:  422);
        }

        return $this->json(
            data: ($buildExportAssetResourceCollection)($request, $organization)->toArray()
        );
    }
}
