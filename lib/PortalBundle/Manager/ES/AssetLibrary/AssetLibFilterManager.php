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
    public function filter(BoolQuery $boolQuery, User $user, ?LibraryFormDto $formDto = null): BoolQuery
    {
        $this->filterByUserOrganizations($boolQuery, $user);

        $this->addDynamicFilters($boolQuery, $formDto);

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
    private function addDynamicFilters(BoolQuery $boolQuery, ?LibraryFormDto $libraryFormDto = null): void
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
                $words = (array)preg_split('/\s+/', $filterDto->getText());
                foreach ($words as $word) {
                    $value = sprintf('*%s*', $word);
                    $queryStringQuery = new QueryString($value);
                    $queryStringQuery->setDefaultField((string) $key);
                    $boolQuery->addFilter($queryStringQuery);
                }
            } else {
                throw new \InvalidArgumentException('Unsupported Filter DTO: ' . get_class($filterDto));
            }
        }
    }
}
