<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Manager\ES\AssetLibrary;

use Elastica\Query\BoolQuery;
use Elastica\Query\QueryString;
use Elastica\Query\Range as RangeQuery;
use Elastica\Query\Terms as TermsQuery;
use Froq\PortalBundle\DTO\FormData\DateRangeFilterDto;
use Froq\PortalBundle\DTO\FormData\InputFilterDto;
use Froq\PortalBundle\DTO\FormData\LibraryFormDto;
use Froq\PortalBundle\DTO\FormData\MultiselectCheckboxFilterDto;
use Froq\PortalBundle\DTO\FormData\NumberRangeFilterDto;
use Froq\PortalBundle\Repository\UserRepository;
use Pimcore\Model\DataObject\User;

class AssetLibFilterManager
{
    public function __construct(private readonly UserRepository $userRepo)
    {
    }

    /**
     * @param BoolQuery $boolQuery
     * @param User $user
     * @param LibraryFormDto|null $formDto
     *
     * @return BoolQuery
     */
    public function filter(BoolQuery $boolQuery, User $user, ?LibraryFormDto $formDto, bool &$sortByRelevance): BoolQuery
    {
        $this->filterByUserOrganizations($boolQuery, $user);

        $this->addDynamicFilters($boolQuery, $formDto, $sortByRelevance);

        return $boolQuery;
    }

    /**
     * @param BoolQuery $boolQuery
     * @param User $user
     *
     * @return void
     */
    private function filterByUserOrganizations(BoolQuery $boolQuery, User $user): void
    {
        $boolQuery->addFilter(new TermsQuery(AssetLibMappingManager::MAPPING_ORGANIZATION_ID, $this->userRepo->getOrganizationIDs($user)));
    }

    /**
     * @param BoolQuery $boolQuery
     * @param LibraryFormDto|null $libraryFormDto
     *
     * @return void
     */
    private function addDynamicFilters(BoolQuery $boolQuery, ?LibraryFormDto $libraryFormDto, bool &$sortByRelevance): void
    {
        if (!$libraryFormDto?->getFilters()) {
            return;
        }

        foreach ($libraryFormDto->getFilters() as $key => $filterDto) {
            if (!$filterDto) {
                continue;
            }

            if ($filterDto instanceof NumberRangeFilterDto) {
                $options = [];
                if ($filterDto->getMin()) {
                    $options['gte'] = $filterDto->getMin();
                }
                if ($filterDto->getMax()) {
                    $options['lte'] = $filterDto->getMax();
                }
                if ($options) {
                    $boolQuery->addFilter(new RangeQuery((string)$key, $options));
                }
            } elseif ($filterDto instanceof DateRangeFilterDto) {
                $options = [];
                if ($filterDto->getStartDate()) {
                    $options['gte'] = $filterDto->getStartDate()->setTimezone(new \DateTimeZone('UTC'))->getTimestamp();
                }
                if ($filterDto->getEndDate()) {
                    $options['lte'] = $filterDto->getEndDate()->modify('+1 day')->setTimezone(new \DateTimeZone('UTC'))->getTimestamp();
                }
                if ($options) {
                    $boolQuery->addFilter(new RangeQuery((string)$key, $options));
                }
            } elseif ($filterDto instanceof MultiselectCheckboxFilterDto && $filterDto->getSelectedOptions()) {
                $boolQuery->addFilter(new TermsQuery((string)$key, $filterDto->getSelectedOptions()));
            } elseif ($filterDto instanceof InputFilterDto && $filterDto->getText()) {
                $searchTerm = (string) $filterDto->getText();

                $isFilename = preg_match('/^[a-zA-Z0-9._-]+$/', $searchTerm)
                    && preg_match('/\./', $searchTerm)
                    && ($key === 'file_name' || $key === 'file_name_text');

                if ($isFilename && !preg_match('/\b(AND|OR|NOT)\b/', $searchTerm)) {
                    $searchTerm = preg_replace('/\s+/', ' AND ', $searchTerm);
                    $queryStringQuery = new QueryString("file_name:$searchTerm");
                    $boolQuery->addFilter($queryStringQuery);

                    return;
                }

                if (!preg_match('/\b(AND|OR|NOT)\b/', $searchTerm)) {
                    $searchTerm = preg_replace('/\s+/', ' AND ', $searchTerm);

                    $queryStringQuery = new QueryString("$key:$searchTerm");
                    $boolQuery->addFilter($queryStringQuery);

                    return;
                }

                $queryStringQuery = new QueryString("$key:$searchTerm");
                $boolQuery->addFilter($queryStringQuery);

                $sortByRelevance = true;
            } else {
                throw new \InvalidArgumentException('Unsupported Filter DTO: ' . get_class($filterDto));
            }
        }
    }
}
