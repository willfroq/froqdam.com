<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Controller;

use Froq\PortalBundle\Api\Action\BuildAssetResourceItems;
use Froq\PortalBundle\Api\Action\GetApiUser;
use Froq\PortalBundle\Api\Action\GetPaginator;
use Froq\PortalBundle\Api\Request\AssetLibraryRequest;
use Froq\PortalBundle\Api\Validator\GetValidationErrors;
use Froq\PortalBundle\Api\ValueObject\AssetResource\AssetResourceCollection;
use Froq\PortalBundle\DTO\FormData\LibraryFormDto;
use Froq\PortalBundle\Manager\ES\AssetLibrary\AssetLibFormManager;
use Froq\PortalBundle\Manager\ES\AssetLibrary\AssetLibQueryBuilderManager;
use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/assets', name: 'froq_portal_api.assets')]
final class IndexController extends AbstractController
{
    public function __construct(
        private readonly AssetLibQueryBuilderManager $assetLibQueryBuilder,
        private readonly AssetLibFormManager $assetLibFormManager,
        private readonly LibraryFormDto $libraryFormDto,
    ) {
    }

    public function __invoke(
        Request $request,
        GetValidationErrors $getValidationErrors,
        GetPaginator $getPaginator,
        BuildAssetResourceItems $buildAssetResourceItems,
        #[CurrentUser] User $currentUser,
        GetApiUser $getApiUser
    ): JsonResponse {
        $user = ($getApiUser)($currentUser, (string) $request->get('code'));

        $validationErrors = ($getValidationErrors)(new AssetLibraryRequest(
            user: $user,
            hasOrganization: !empty($user?->getOrganizations())
        ));

        if (count($validationErrors) > 0) {
            return $this->json(data: ['validationErrors' => $validationErrors], status:  422);
        }

        $initialQueryResponseDto = null;
        $filtersMetadata = null;
        $sortChoices = null;

        if ($user instanceof User) {
            $formDto = $this->assetLibFormManager->populateLibraryFormDtoFromRequest($user, $this->libraryFormDto, $request);

            $initialQueryResponseDto = $this->assetLibQueryBuilder->search($user, $formDto);

            $filtersMetadata = $this->assetLibFormManager->buildFormFiltersMetadata($user, (array) $initialQueryResponseDto?->getAggregationDTOs());

            $sortChoices = $this->assetLibFormManager->buildFormSortChoices($user);
        }

        if (!$initialQueryResponseDto) {
            return $this->json(data: ['message' => 'No Record Found'], status: 204);
        }

        // TODO Handle other filters and aggregations here
        $queryResponseDto = $initialQueryResponseDto;

        return $this->json(data: [
            'assetResourceCollection' => new AssetResourceCollection(
                totalCount: $queryResponseDto->getTotalCount(),
                items: ($buildAssetResourceItems)($queryResponseDto->getObjects()),
            ),
            'filtersMetadata' => $filtersMetadata,
            'sortChoices' => $sortChoices,
            'pagination' => ($getPaginator)($request, $queryResponseDto)
        ]);
    }
}
