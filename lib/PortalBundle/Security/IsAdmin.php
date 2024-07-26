<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Security;

use Pimcore\Model\DataObject\User;
use Pimcore\Model\DataObject\User\Listing;

class IsAdmin
{
    public function __invoke(User $user): bool
    {
        if (!str_contains(haystack: (string) $user->getPath(), needle: 'Admin')) {
            return false;
        }

        return (new Listing())
            ->addConditionParam('`o_key` = ?', $user->getKey())
            ->addConditionParam('`o_path` = ?', $user->getPath())
            ->current() instanceof User;
    }
}
