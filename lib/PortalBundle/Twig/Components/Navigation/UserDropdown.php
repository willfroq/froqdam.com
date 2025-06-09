<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Navigation;

use Pimcore\Model\DataObject\User;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'UserDropdown', template: '@FroqPortal/components/navigation/UserDropdown.html.twig')]
final class UserDropdown
{
    public User $user;

    public function getFullname(): string
    {
        return $this->user->getUserName() ?? '';
    }

    public function getInitials(): string
    {
        $name = $this->getFullname();

        if (empty($name)) {
            return '';
        }

        $words = explode(' ', $name);

        return array_reduce($words, function ($carry, $word) {
            return $carry . strtoupper(substr($word, 0, 1));
        }, '');
    }
}
