<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\Action\Search\Builder;

use Froq\AssetBundle\Pimtoday\Action\Search\GetSearchResultSet;
use Froq\AssetBundle\Pimtoday\Controller\Request\SearchRequest;
use Froq\AssetBundle\Pimtoday\Enum\ThumbnailTypes;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResource\AssetResourceCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\AssetResource\AssetResourceItem;
use Froq\AssetBundle\Pimtoday\ValueObject\Filters\MulticheckboxCollection;
use Froq\AssetBundle\Pimtoday\ValueObject\Filters\MulticheckboxItem;
use Froq\PortalBundle\Twig\AssetPreviewExtension;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\Project;
use Pimcore\Model\DataObject\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BuildAssetResourceCollection
{
    public function __construct(
        private readonly GetSearchResultSet $getSearchResultSet,
        private readonly AssetPreviewExtension $assetPreviewExtension,
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(SearchRequest $searchRequest, Organization $organization, #[CurrentUser] User $user): AssetResourceCollection
    {

        $searchResultSet = ($this->getSearchResultSet)($searchRequest, $organization, $user);

        $assetResourceItems = [];

        foreach ($searchResultSet ?? [] as $result) {
            $assetResource = $result->getElement();

            if (!($assetResource instanceof AssetResource)) {
                continue;
            }

            $project = current($assetResource->getProjects());

            $children = $assetResource->getChildren();

            $isParent = count($children) > 0;

            if ($isParent) {
                $assetResource = end($children);
            }

            if (!($assetResource instanceof AssetResource)) {
                continue;
            }

            $asset = $assetResource->getAsset();

            if (!($asset instanceof Asset)) {
                continue;
            }

            $request = $this->requestStack->getCurrentRequest();
            $domain = $request?->getSchemeAndHttpHost();

            $asserResourceId = $assetResource->getId();

            $assetResourceItems[] = new AssetResourceItem(
                assetResourceId: (int) $asserResourceId,
                thumbnailLinks: [
                    'grid' => $domain.$this->assetPreviewExtension->getAssetThumbnailHashedURL($asset, ThumbnailTypes::Grid->value),
                    'list' => $domain.$this->assetPreviewExtension->getAssetThumbnailHashedURL($asset, ThumbnailTypes::List->value),
                ],
                filename: (string) $asset->getFilename(),
                assetType: (string) $assetResource->getAssetType()?->getName(),
                projectName: $project instanceof Project ? (string) $project->getName() : '',
                downloadLink: $this->urlGenerator->generate(
                    'froq_portal.asset_library.detail.download.file',
                    ['id' => $asserResourceId],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                creationDate: date('l jS \o\f F Y h:i:s A', $assetResource->getCreationDate()),
                fileCreationDate: date('l jS \o\f F Y h:i:s A', $assetResource->getFileCreateDate()?->getTimestamp()),
            );
        }

        $aggregations = $searchResultSet?->getRawElasticsearchResponse()['aggregations'] ?? [];

        $multicheckboxes = [];

        foreach ($aggregations as $filterName => $aggregation) {
            $buckets = $aggregation['buckets'] ?? [];

            $items = [];
            foreach ($buckets as $bucket) {
                $items[] = new MulticheckboxItem(
                    label: $bucket['key'] ?? '',
                    filterName: $filterName,
                    count: $bucket['doc_count'] ?? 0,
                );
            }

            $multicheckboxes[] = new MulticheckboxCollection(
                filterName: $filterName,
                totalCount: count($items),
                items: $items
            );
        }

        return new AssetResourceCollection(
            totalCount: (int) $searchResultSet?->getTotalCount(),
            size: (int) $searchRequest->size,
            page: (int) $searchRequest->page,
            multicheckboxes: $multicheckboxes,
            inputs: [],
            dates: [],
            ranges: [],
            items: $assetResourceItems,
        );
    }
}
