<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Controller;

use Froq\AssetBundle\Pimtoday\Action\Search\Builder\BuildAssetResourceCollection;
use Froq\AssetBundle\Pimtoday\Action\Search\Builder\BuildSearchRequest;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/asset-library/{organizationId}', name: 'froq_portal_pimtoday.pimtoday_search', methods: [Request::METHOD_GET])]
final class SearchController extends AbstractController
{
    /**
     * @throws \Exception
     */
    public function __invoke(Request $request, BuildSearchRequest $buildSearchRequest, BuildAssetResourceCollection $buildAssetResourceCollection, #[CurrentUser] User $user, int $organizationId): JsonResponse
    {
        $validatedRequest = ($buildSearchRequest)($request);

        if ($validatedRequest->hasErrors) {
            throw $this->createNotFoundException(message: 'Page not found.');
        }

        $organization = Organization::getById($organizationId);

        if (!($organization instanceof Organization)) {
            throw $this->createNotFoundException(message: 'Page not found.');
        }

        return $this->json(data: ($buildAssetResourceCollection)($validatedRequest, $organization, $user)->toArray());
    }
}
