<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Action;

use Carbon\Carbon;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\User;
use Pimcore\Model\DataObject\UserGroup;
use TheNetworg\OAuth2\Client\Provider\AzureResourceOwner;

final class CreateUserFromAzurePayload
{
    /**
     * @throws \Exception
     */
    public function __invoke(AzureResourceOwner $azureUser): User
    {
        $userGroup = UserGroup::getByName('NewlyLoggedInUsers')?->current(); /** @phpstan-ignore-line */
        $groupAssetLibrary = GroupAssetLibrarySettings::getByGroupName('GPC/GPA')?->current(); /** @phpstan-ignore-line */
        if (!($userGroup instanceof UserGroup)) {
            throw new \Exception(message: 'Can not create portal User without UserGroup.');
        }

        if (!($groupAssetLibrary instanceof GroupAssetLibrarySettings)) {
            throw new \Exception(message: 'Can not create portal User without GroupAssetLibrarySettings.');
        }

        $user = User::create();

        $user->setForeignUserId('n/a');
        $user->setCode($azureUser->getUpn());
        $user->setName($azureUser->getFirstName());
        $user->setUserName($azureUser->getUpn());
        $user->setEmail((string) $azureUser->getUpn());
        $user->setPassword(md5((string) $azureUser->getUpn()));
        $user->setLastLogin(Carbon::now());
        $user->setParent($userGroup);
        $user->setKey((string) $azureUser->getUpn());
        $user->setOrganizations(Organization::getList()->getObjects());
        $user->setGroupAssetLibrarySettings($groupAssetLibrary);
        $user->setPublished(true);

        $userGroup->setRoles((array) $azureUser->claim('roles'));

        $user->save();
        $userGroup->save();

        return $user;
    }
}
