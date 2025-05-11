<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Export\Action;

use Froq\AssetBundle\Export\DataTransferObject\ClientExportCollection;
use Froq\AssetBundle\Export\DataTransferObject\OrganizationExportItem;
use Froq\AssetBundle\Export\DataTransferObject\UserExportItem;
use Froq\AssetBundle\Export\DataTransferObject\UserGroupExportCollection;
use Froq\AssetBundle\Export\DataTransferObject\UserGroupExportItem;
use Pimcore\Model\DataObject\GroupAssetLibrarySettings;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\User;
use Pimcore\Model\DataObject\UserGroup;

final class BuildExportUserGroupCollection
{
    public function __construct(private readonly BuildGroupAssetLibrarySettingsExportItem $buildGroupAssetLibrarySettingsExportItem)
    {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(int $offset, int $limit): UserGroupExportCollection
    {
        $userGroups = UserGroup::getList()
            ->setOffset($offset)
            ->setLimit($limit)
            ->setOrderKey('Name')
            ->setOrder('asc');

        $userGroupExportItems = [];
        $clients = null;

        foreach ($userGroups as $userGroup) {
            if (!($userGroup instanceof UserGroup)) {
                continue;
            }

            if ($userGroup->getKey() === 'Clients') {
                $clients = $this->createClients($userGroup);

                continue;
            }

            $userGroupExportItems[] = new UserGroupExportItem(
                id: (int) $userGroup->getId(),
                key: (string) $userGroup->getKey(),
                path: (string) $userGroup->getPath(),
                name: (string) $userGroup->getName(),
                users: $this->createUserExportItems($userGroup),
                roles: (array) $userGroup->getRoles()
            );
        }

        return new UserGroupExportCollection(
            offset: $offset,
            limit: $limit,
            totalCount: $userGroups->count(),
            userGroupExportItems: $userGroupExportItems,
            clients: $clients
        );
    }

    private function createClients(UserGroup $userGroup): ClientExportCollection
    {
        $userGroupExportItems = [];
        $clientUserExportItems = [];

        foreach ($userGroup->getChildren() as $item) {
            if ($item instanceof UserGroup) {
                $userGroupExportItems[] = new UserGroupExportItem(
                    id: (int) $userGroup->getId(),
                    key: (string) $userGroup->getKey(),
                    path: (string) $userGroup->getPath(),
                    name: (string) $userGroup->getName(),
                    users: $this->createUserExportItems($userGroup),
                    roles: (array) $userGroup->getRoles()
                );

                continue;
            }

            if (!($item instanceof User)) {
                continue;
            }

            $clientUserExportItems[] = $this->getUserExportItem($item);
        }

        return new ClientExportCollection(
            userExportItems: $clientUserExportItems,
            userGroupExportItems: $userGroupExportItems
        );
    }

    /** @return array<int, UserExportItem> */
    private function createUserExportItems(UserGroup $userGroup): array
    {
        $userExportItems = [];

        foreach ($userGroup->getChildren() as $user) {
            if (!($user instanceof User)) {
                continue;
            }

            $userExportItems[] = $this->getUserExportItem($user);
        }

        return $userExportItems;
    }

    private function getUserExportItem(User $user): UserExportItem
    {
        $settings = $user->getGroupAssetLibrarySettings() instanceof GroupAssetLibrarySettings
            ? ($this->buildGroupAssetLibrarySettingsExportItem)($user->getGroupAssetLibrarySettings())
            : null;

        return new UserExportItem(
            id: (int) $user->getId(),
            foreignUserId: (string) $user->getForeignUserId(),
            key: (string) $user->getKey(),
            path: (string) $user->getPath(),
            name: (string) $user->getName(),
            address: (string) $user->getAddress(),
            username: (string) $user->getUserName(),
            email: (string) $user->getEmail(),
            secondaryEmail: (string) $user->getSecondaryEmail(),
            confirmationToken: (string) $user->getConfirmationToken(),
            lastLogin: (string) $user->getLastLogin()?->timestamp,
            passwordRequestedAt: (string) $user->getPasswordRequestedAt()?->timestamp,
            groupAssetLibrarySettingsExportItem: $settings,
            organizationExportItems: $this->createOrganizationExportItems($user)
        );
    }

    /** @return array<int, OrganizationExportItem> */
    private function createOrganizationExportItems(User $user): array
    {
        $organizationExportItem = [];

        foreach ($user->getOrganizations() as $organization) {
            if (!($organization instanceof Organization)) {
                continue;
            }

            $organizationExportItem[] = new OrganizationExportItem(
                id: (int) $organization->getId(),
                code: (int) $organization->getCode(),
                mainContactId: $organization->getMainContact()?->getId(),
                name: (string) $organization->getName(),
                key: (string) $organization->getKey(),
                path: (string) $organization->getPath(),
                objectFolder: (string) $organization->getObjectFolder(),
                assetFolder: (string) $organization->getAssetFolder()
            );
        }

        return $organizationExportItem;
    }
}
