<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Controller;

use Froq\AssetBundle\Pimtoday\Action\Search\Builder\BuildDetailResponse;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/detail/{assetResourceId<\d+>}/{organizationId<\d+>}', name: 'froq_portal_pimtoday.pimtoday_detail', methods: [Request::METHOD_GET])]
final class DetailController extends AbstractController
{
    public function __invoke(
        Request $request,
        BuildDetailResponse $buildDetailResponse,
        int $assetResourceId,
        int $organizationId
    ): JsonResponse {
        $organization = Organization::getById($organizationId);

        if (!($organization instanceof Organization)) {
            throw $this->createNotFoundException(message: 'Page not found.');
        }

        $assetResource = AssetResource::getById($assetResourceId);

        if (!($assetResource instanceof AssetResource)) {
            throw $this->createNotFoundException(message: 'Page not found.');
        }

        $response = ($buildDetailResponse)($request, $assetResource);

        if ($response->hasErrors) {
            throw $this->createNotFoundException(message: 'Page not found.');
        }

        return $this->json(data: $response->toArray());
    }
}
