<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Api\Action;

use Pimcore\Model\DataObject\User;

final class GetApiUser
{
    public function __invoke(User $currentUser, string $code): ?User
    {
        // Token that came from Azure ADD
        /** @var User\Listing $damUsers */
        $damUsers = User::getByCode($code);

        /** @var User|null $user */
        $user = $damUsers?->current() instanceof User ? $damUsers?->current() : null; /** @phpstan-ignore-line */
        if ($damUsers->count() > 1 || $user?->getId() !== $currentUser->getId()) {
            $user = null;
        }

        return $user;
    }
}
