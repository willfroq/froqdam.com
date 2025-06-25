<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Action\Setting;

use Froq\PortalBundle\Opensearch\Enum\SchemaNames;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\User;

final class GetUserSettings
{
    public function __invoke(User $user): ?GroupAssetLibrarySettings
    {
        $settings = $user->getGroupAssetLibrarySettings();

        if ($settings === null) {
            $relation = current($user->getRelationData(SchemaNames::GroupAssetLibrarySettings->readable(), true, ''));

            if (!isset($relation['id']) || !isset($relation['published']) || $relation['published'] !== '1') {
                return null;
            }

            $settingsId = $relation['id'];

            $settings = GroupAssetLibrarySettings::getById($settingsId);
        }

        return $settings;
    }
}
