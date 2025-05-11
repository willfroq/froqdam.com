<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\ES\AssetLibrary;

use Froq\PortalBundle\DTO\AggregationChoiceDto;
use Froq\PortalBundle\DTO\FormData\DateRangeFilterDto;
use Froq\PortalBundle\DTO\FormData\FilterMetadataDto;
use Froq\PortalBundle\DTO\FormData\InputFilterDto;
use Froq\PortalBundle\DTO\FormData\LibraryFormDto;
use Froq\PortalBundle\DTO\FormData\MultiselectCheckboxFilterDto;
use Froq\PortalBundle\DTO\FormData\NumberRangeFilterDto;
use Froq\PortalBundle\DTO\QueryResponseDto;
use Froq\PortalBundle\ESPropertyMapping\MappingTypes;
use Pimcore\Model\DataObject\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AssetLibFormManager
{
    private const ASSET_LIB_SESSION_AGGREGATIONS = 'ASSET_LIB_SESSION_AGGREGATIONS';
    private const ASSET_LIB_SESSION_SEARCH_QUERY = 'ASSET_LIB_SESSION_SEARCH_QUERY';

    public function __construct(private readonly AssetLibMappingManager $mappingManager,
        private readonly AssetLibSortManager $assetLibSortManager,
        private readonly SessionInterface $session)
    {
    }

    /**
     *
     * @param User $user
     * @param LibraryFormDto $libraryFormDto
     * @param Request $request
     *
     * @return LibraryFormDto
     */
    public function populateLibraryFormDtoFromRequest(User $user, LibraryFormDto $libraryFormDto, Request $request): LibraryFormDto
    {
        $filterDTOs = [];

        $libraryFormDto->setQuery($request->query->get('query'));
        $libraryFormDto->setPage($request->query->get('page'));
        $libraryFormDto->setSize($request->query->get('size'));
        $libraryFormDto->setSortBy($request->query->get('sort_by'));
        $libraryFormDto->setSortDirection($request->query->get('sort_direction'));

        $requestFilters = (array) $request->query->get('filters', '');

        foreach ($requestFilters as $filterKey => $filterValues) {
            $filterDto = null;
            $type = $this->getFilterTypeByKey($user, (string) $filterKey);

            switch ($type) {
                case 'input':
                    $filterDto = new InputFilterDto();
                    $filterDto->setText($filterValues['text'] ?? null);
                    $filterDto->setField((string) $filterKey);
                    break;
                case 'keyword':
                    $filterDto = new MultiselectCheckboxFilterDto();
                    $filterDto->setSelectedOptions((array) $filterValues);
                    break;
                case 'date':
                    $filterDto = new DateRangeFilterDto();
                    if (isset($filterValues['startDate'])) {
                        $filterDto->setStartDate(new \DateTime($filterValues['startDate']));
                    }
                    if (isset($filterValues['endDate'])) {
                        $filterDto->setEndDate(new \DateTime($filterValues['endDate']));
                    }
                    break;
                case 'integer':
                    $filterDto = new NumberRangeFilterDto();
                    if (isset($filterValues['min'])) {
                        $filterDto->setMin((float) $filterValues['min']);
                    }
                    if (isset($filterValues['max'])) {
                        $filterDto->setMax((float) $filterValues['max']);
                    }
                    break;
                default:
                    break;
            }

            if ($filterDto) {
                $filterDTOs[$filterKey] = $filterDto;
            }
        }

        if ($filterDTOs) {
            $libraryFormDto->setFilters($filterDTOs);
        }

        return $libraryFormDto;
    }

    public function updateSessionData(Request $request, QueryResponseDto $dto): void
    {
        if (!$this->shouldUpdateSessionData($request)) {
            return;
        }

        $this->session->set(self::ASSET_LIB_SESSION_AGGREGATIONS, serialize($dto->getAggregationDTOs()));
        $this->session->set(self::ASSET_LIB_SESSION_SEARCH_QUERY, $request->query->get('query'));
    }

    /**
     * @param User $user
     * @param array<array<int<0, max>, AggregationChoiceDto>> $aggregationChoices
     *
     * @return array<int|string, FilterMetadataDto>
     */
    public function buildFormFiltersMetadata(User $user, array $aggregationChoices = []): array
    {
        $filters = [];

        foreach ($this->mappingManager->getFiltersMapping($user) as $fieldId => $data) {
            $type = $data['type'];

            $fieldMetadata = new FilterMetadataDto();
            $fieldMetadata->setFieldName((string) $fieldId);
            if (!empty($aggregationChoices[$fieldId])) {
                $fieldMetadata->setAggregationChoices($aggregationChoices[$fieldId]);
            }

            if ($type === MappingTypes::MAPPING_TYPE_NESTED) {
                $fieldProperties = $data['properties'];
                foreach ($fieldProperties as $propertyData) {
                    if ($propertyData['type'] === MappingTypes::MAPPING_TYPE_KEYWORD) {
                        $fieldMetadata->setType($propertyData['type']);
                        $filters[$fieldId] = $fieldMetadata;
                        break;
                    }
                }
            } else {
                $fieldMetadata->setType($type);
                $filters[$fieldId] = $fieldMetadata;
            }
        }

        $netContentsFilterNames = [
            'net_contents_ml',
            'net_contents_g',
            'net_contents_pcs',
            'net_unit_contents_ml',
            'net_unit_contents_g',
        ];

        foreach ($netContentsFilterNames as $netContentsFilterName) {
            $filterMetadataDto = $filters[$netContentsFilterName] ?? null;

            if (!($filterMetadataDto instanceof FilterMetadataDto)) {
                continue;
            }

            $checkboxes = $filterMetadataDto->getAggregationChoices();

            usort($checkboxes, function ($aggregationChoiceDtoPrev, $aggregationChoiceDtoNext) {
                if (!($aggregationChoiceDtoPrev instanceof AggregationChoiceDto)) {
                    return 0;
                }

                if (!($aggregationChoiceDtoNext instanceof AggregationChoiceDto)) {
                    return 0;
                }

                $numA = (int) preg_replace('/\D/', '', $aggregationChoiceDtoPrev->getKey());
                $numB = (int) preg_replace('/\D/', '', $aggregationChoiceDtoNext->getKey());

                return $numA <=> $numB;
            });

            $filterMetadataDto->setAggregationChoices($checkboxes);
        }

        return $filters;
    }

    /**
     * @param User $user
     *
     * @return array<string, int|string>
     */
    public function buildFormSortChoices(User $user): array
    {
        $choices = [];

        foreach ($this->assetLibSortManager->getSortableItems($user) as $id => $label) {
            $choices[$label] = $id;
        }

        return $choices;
    }

    /**
     * @param array<int|string, mixed> $filteredAggDtos
     *
     * @return array<array<int<0, max>, AggregationChoiceDto>>
     */
    public function mergeCachedAndFilteredAggregations(LibraryFormDto $libraryFormDto, array $filteredAggDtos): array
    {
        $cachedAggDtos = unserialize($this->session->get(self::ASSET_LIB_SESSION_AGGREGATIONS));

        foreach ($libraryFormDto->getFilters() as $key => $filter) {
            if ($filter && isset($cachedAggDtos[$key]) && isset($filteredAggDtos[$key])) {
                $updatedDtos = [];
                /** @var AggregationChoiceDto $cachedDto */
                foreach ($cachedAggDtos[$key] as $cachedDto) {
                    $dtoKey = $cachedDto->getKey();
                    $docCount = $cachedDto->getDocCount();
                    /** @var AggregationChoiceDto $filteredDto */
                    foreach ((array) $filteredAggDtos[$key] as $filteredDto) {
                        if ($filteredDto->getKey() === $cachedDto->getKey()) {
                            $docCount = $filteredDto->getDocCount();
                            break;
                        }
                    }
                    $updatedDtos[] = new AggregationChoiceDto($dtoKey, $docCount);
                }

                $filteredAggDtos[$key] = $updatedDtos;
            }
        }

        $this->session->set(self::ASSET_LIB_SESSION_AGGREGATIONS, serialize($filteredAggDtos));

        return $filteredAggDtos;
    }

    private function shouldUpdateSessionData(Request $request): bool
    {
        if ($this->cameFromAssetDetailPage($request)) {
            return true;
        }

        if (!$this->session->get(self::ASSET_LIB_SESSION_AGGREGATIONS)) {
            return true;
        }

        if (!$request->query->get('filters')) {
            return true;
        }

        if ($request->query->get('query') !== $this->session->get(self::ASSET_LIB_SESSION_SEARCH_QUERY)) {
            return true;
        }

        return false;
    }

    private function getFilterTypeByKey(User $user, string $filterKey): ?string
    {
        $type = null;
        $filters = $this->mappingManager->getFiltersMapping($user);

        if (isset($filters[$filterKey])) {
            $type = $filters[$filterKey]['type'];
        }

        return $type;
    }

    private function cameFromAssetDetailPage(Request $request): bool
    {
        $refererComponents = parse_url($request->headers->get('referer') ?? '');

        if (isset($refererComponents['path'])) {
            $pattern = '/\/portal\/asset-library\/detail\/\d+/';
            if (preg_match($pattern, $refererComponents['path'])) {
                return true;
            }
        }

        return false;
    }
}
