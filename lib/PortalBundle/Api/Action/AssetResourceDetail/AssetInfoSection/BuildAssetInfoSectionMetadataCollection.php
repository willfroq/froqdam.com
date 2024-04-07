<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action\AssetResourceDetail\AssetInfoSection;

use Froq\AssetBundle\Manager\AssetResource\AssetResourceFieldCollectionsManager;
use Froq\PortalBundle\Api\Action\GetBaseUrl;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionMetadataCollection;
use Froq\PortalBundle\Api\ValueObject\AssetResourceDetail\AssetInfoSection\AssetInfoSectionMetadataItem;
use Froq\PortalBundle\Twig\AssetLibraryExtension;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Fieldcollection\Data\SettingsMetadata;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class BuildAssetInfoSectionMetadataCollection
{
    public function __construct(
        private readonly AssetLibraryExtension $assetLibraryExtension,
        private readonly AssetResourceFieldCollectionsManager $assetResourceFieldCollectionsManager,
        private readonly GetBaseUrl $getBaseUrl,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(AssetResource $assetResource, User $user, GroupAssetLibrarySettings $userSetting): AssetInfoSectionMetadataCollection
    {
        $items = [];

        foreach ($userSetting->getAssetInfoSectionMetadata()?->getItems() ?? [] as $item) {
            if (!($item instanceof SettingsMetadata)) {
                continue;
            }

            $key = (string) $item->getMetadataKey();

            $isFilterAvailableForUser = $this->assetLibraryExtension->isFilterAvailableForUser($user, (string) $item->getMetadataKey());

            $items[] = new AssetInfoSectionMetadataItem(
                key: $key,
                label: (string) $item->getLabel(),
                isAvailableForUser: $isFilterAvailableForUser,
                linkValue: (string) $isFilterAvailableForUser ? ($this->getBaseUrl)() . $this->urlGenerator->generate('froq_portal_api.assets', [
                        'code' => $user->getCode(),
                        'filters' => [
                            'asset_type_name' => [strtolower((string) $assetResource->getAssetType()?->getName())]
                        ]
                    ]) : '',
                value: (string) $this->assetResourceFieldCollectionsManager->getMetadataValueByKey($assetResource, $key)
            );
        }

        return new AssetInfoSectionMetadataCollection(
            items: $items,
        );
    }
}
