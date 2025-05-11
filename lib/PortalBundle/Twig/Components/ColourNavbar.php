<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components;

use Pimcore\Model\DataObject\User;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ColourNavbar', template: '@FroqPortal/components/ColourNavbar.html.twig')]
final class ColourNavbar
{
    public User $user;
}
