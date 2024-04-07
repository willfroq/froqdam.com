<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Voter\AssetResource;

use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ViewByOrganizationVoter extends Voter
{
    public const VIEW = 'view';

    /**
     * @template T of mixed
     *
     * @param class-string<T> $attribute
     *
     * @phpstan-assert-if-true T $subject
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute !== self::VIEW) {
            return false;
        }

        if (!$subject instanceof AssetResource) {
            return false;
        }

        return true;
    }

    /**
     * @param AssetResource $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        $assetResourceOrganizations = $subject->getOrganizations();

        if (AssetResourceHierarchyHelper::isChild($subject)) {
            /** @phpstan-ignore-next-line */
            $assetResourceOrganizations = array_merge($subject->getParent()->getOrganizations(), $assetResourceOrganizations);
        }

        if (empty($assetResourceOrganizations)) {
            return false;
        }

        $userOrganizations = $user->getOrganizations();

        if (empty($userOrganizations)) {
            return false;
        }

        $assetResourceOrganizations = array_unique($assetResourceOrganizations);

        $result = array_intersect($assetResourceOrganizations, $userOrganizations);

        if (empty($result)) {
            return false;
        }

        return true;
    }
}
