<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Voter\AssetResource;

use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ViewCanBeDisplayedVoter extends Voter
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
        return AssetResourceHierarchyHelper::isParentWithoutChildren($subject)
            || AssetResourceHierarchyHelper::isChild($subject);
    }
}
