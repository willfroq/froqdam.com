<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Controller\AssetLibrary;

use Froq\PortalBundle\Action\ProcessProductContentsRequestFilter;
use Froq\PortalBundle\DTO\AggregationChoiceDto;
use Froq\PortalBundle\DTO\FormData\LibraryFormDto;
use Froq\PortalBundle\DTO\QueryResponseDto;
use Froq\PortalBundle\Form\AssetLibSearchFormType;
use Froq\PortalBundle\Manager\ES\AssetLibrary\AssetLibFormManager;
use Froq\PortalBundle\Manager\ES\AssetLibrary\AssetLibQueryBuilderManager;
use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/search', name: 'froq_portal.asset_library.')]
class SearchController extends AbstractController
{
    public const LAYOUT_LIST = 'list';
    public const LAYOUT_GRID = 'grid';

    public function __construct(
        private readonly AssetLibQueryBuilderManager $assetLibQueryBuilder,
        private readonly AssetLibFormManager $assetLibFormManager,
        private readonly ProcessProductContentsRequestFilter $processProductContentsRequestFilter,
    ) {
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/', name: 'search', methods: ['GET'])]
    public function searchAction(Request $request): Response
    {
        $data = $this->getFormAndQueryResponse($request);
        $type = $request->query->get('type');

        /** @var FormInterface $form */
        $form = $data['form'];

        /** @var QueryResponseDto $queryResponseDto */
        $queryResponseDto = $data['queryResponseDto'];
        $pagination = $this->getPagination($request, $queryResponseDto);

        $templateParams = array_merge(
            $pagination,
            [
                'form' => $form->createView(),
                'items' => $queryResponseDto->getObjects(),
                'user' => $this->getUser(),
                'itemsLayout' => $this->getItemsLayout($type ?? null),
                'totalCount' => $queryResponseDto->getTotalCount()
            ]
        );

        return $this->render('@FroqPortalBundle/asset-library/search.html.twig', $templateParams);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/fetch-form-and-results', name: 'search.fetch_form_and_results', methods: ['GET'])]
    public function fetchFormAndResults(Request $request): Response
    {
        $data = $this->getFormAndQueryResponse($request);
        $type = $request->query->get('type');

        /** @var FormInterface $form */
        $form = $data['form'];
        /** @var QueryResponseDto $queryResponseDto */
        $queryResponseDto = $data['queryResponseDto'];

        $pagination = $this->getPagination($request, $queryResponseDto);

        $templateParams = array_merge(
            $pagination,
            [
                'form' => $form->createView(),
                'items' => $queryResponseDto->getObjects(),
                'user' => $this->getUser(),
                'itemsLayout' => $this->getItemsLayout($type ?? null),
                'totalCount' => $queryResponseDto->getTotalCount()
            ]
        );
        $html = $this->renderView('@FroqPortalBundle/partials/asset-library/search_body.html.twig', $templateParams);
        $responseParams = array_merge($pagination, ['html' => $html]);

        return $this->json($responseParams);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/load-more', name: 'search.load_more', methods: ['GET'])]
    public function loadMoreAction(Request $request): Response
    {
        $data = $this->getFormAndQueryResponse($request);
        $type = $request->query->get('type');

        /** @var QueryResponseDto $queryResponseDto */
        $queryResponseDto = $data['queryResponseDto'];

        $pagination = $this->getPagination($request, $queryResponseDto);

        $templateParams = array_merge(
            $pagination,
            [
                'items' => $queryResponseDto->getObjects(),
                'user' => $this->getUser(),
                'forLoadMore' => true
            ]
        );
        $template = sprintf('@FroqPortalBundle/partials/asset-library/load_%s_items.html.twig', $this->getItemsLayout($type ?? null));
        $html = $this->renderView($template, $templateParams);
        $responseParams = array_merge($pagination, ['html' => $html]);

        return $this->json($responseParams);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/load-items-layout', name: 'search.load_items_layout', methods: ['GET'])]
    public function loadItemsLayoutAction(Request $request): Response
    {
        $data = $this->getFormAndQueryResponse($request);
        $type = $request->query->get('type');

        /** @var QueryResponseDto $queryResponseDto */
        $queryResponseDto = $data['queryResponseDto'];

        $pagination = $this->getPagination($request, $queryResponseDto);

        $templateParams = array_merge(
            $pagination,
            [
                'items' => $queryResponseDto->getObjects(),
                'user' => $this->getUser(),
            ]
        );
        $template = sprintf('@FroqPortalBundle/partials/asset-library/load_%s.html.twig', $this->getItemsLayout($type ?? null));
        $html = $this->renderView($template, $templateParams);
        $responseParams = array_merge($pagination, ['html' => $html]);

        return $this->json($responseParams);
    }

    /**
     * @param Request $request
     *
     * @return array<string|int, mixed>
     */
    private function getFormAndQueryResponse(Request $request): array
    {
        /** @var User $user */
        $user = $this->getUser();

        $request = ($this->processProductContentsRequestFilter)($request);

        $formDto = $this->assetLibFormManager->populateLibraryFormDtoFromRequest($user, new LibraryFormDto(), $request);

        // Retrieve the initial query response to populate the filter options from Elasticsearch
        $initialQueryResponseDto = $this->assetLibQueryBuilder->search($user, $formDto);
        if (!$initialQueryResponseDto) {
            return $this->getFormAndQueryResponseForNoResults($user, $formDto);
        }

        // Store some initial data in the session if required
        $this->assetLibFormManager->updateSessionData($request, $initialQueryResponseDto);

        // Create the form and validate the request parameters based on available Elasticsearch fields for the user
        $form = $this->createPreSubmitSearchForm($user, $formDto, $initialQueryResponseDto->getAggregationDTOs());
        $form->submit($request->query->all());
        if ($form->isValid()) {
            $formDto = $form->getData();
            // Perform the search with the applied filters, sorting, and pagination based on the user's input
            $queryResponseDto = $this->assetLibQueryBuilder->search($user, $formDto);
            if ($queryResponseDto) {
                return [
                    'form' => $this->createPostSubmitSearchForm($user, $formDto, $queryResponseDto->getAggregationDTOs()),
                    'queryResponseDto' => $queryResponseDto,
                ];
            }
        }

        return $this->getFormAndQueryResponseForNoResults($user, $formDto);
    }

    /**
     * @param User $user
     * @param LibraryFormDto $libraryFormDto
     * @param array<array<int<0, max>, AggregationChoiceDto>> $aggregationDTOs
     *
     * @return FormInterface
     */
    private function createPreSubmitSearchForm(User $user, LibraryFormDto $libraryFormDto, array $aggregationDTOs): FormInterface
    {
        return $this->createForm(AssetLibSearchFormType::class, $libraryFormDto, [
            'method' => 'GET',
            'user' => $user,
            'filters_metadata' => $this->assetLibFormManager->buildFormFiltersMetadata($user, $aggregationDTOs),
            'sort_choices' => $this->assetLibFormManager->buildFormSortChoices($user)
        ]);
    }

    /**
     * @param array<array<int<0, max>, AggregationChoiceDto>> $filteredAggDtos
     */
    private function createPostSubmitSearchForm(User $user, LibraryFormDto $libraryFormDto, array $filteredAggDtos): FormInterface
    {
        // Combine new and cached aggregations for a broader set of filter options
        $mergedAggregations = $this->assetLibFormManager->mergeCachedAndFilteredAggregations($libraryFormDto, $filteredAggDtos);
        $filteredMetadata = $this->assetLibFormManager->buildFormFiltersMetadata($user, $mergedAggregations);

        return $this->createForm(AssetLibSearchFormType::class, $libraryFormDto, [
            'method' => 'GET',
            'user' => $user,
            'filters_metadata' => $filteredMetadata,
            'sort_choices' => $this->assetLibFormManager->buildFormSortChoices($user)
        ]);
    }

    /**
     * @return array<string|int, mixed>
     */
    private function getFormAndQueryResponseForNoResults(User $user, LibraryFormDto $formDto): array
    {
        return [
            'form' => $this->createPreSubmitSearchForm($user, $formDto, []),
            'queryResponseDto' => new QueryResponseDto(),
        ];
    }

    /**
     * @param string|null $type
     *
     * @return string
     */
    private function getItemsLayout(?string $type = null): string
    {
        if ($type === self::LAYOUT_LIST) {
            return self::LAYOUT_LIST;
        }

        return self::LAYOUT_GRID;
    }

    /**
     * @return array<string|int, mixed>
     */
    private function getPagination(Request $request, QueryResponseDto $queryResponseDto): array
    {
        $page = (int)$request->get('page', 1);
        $page = max(1, $page);

        $size = (int)$request->get('size', AssetLibSearchFormType::DEFAULT_PAGE_SIZE);
        $size = max(1, $size);

        $totalItems = $queryResponseDto->getTotalCount();
        $maxPages = ($totalItems > 0) ? ceil($totalItems / $size) : 1;
        $nextPage = $page < $maxPages ? ($page + 1) : false;

        return [
            'pages' => $maxPages,
            'next_page' => $nextPage,
            'page_size' => $size
        ];
    }
}
