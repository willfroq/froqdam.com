<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components;

use Pimcore\Model\DataObject\User;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Navbar', template: '@FroqPortal/components/Navbar.html.twig')]
final class Navbar
{
    public User $user;

    public string $logoLink;
}
