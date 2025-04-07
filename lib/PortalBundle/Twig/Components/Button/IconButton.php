<?php

namespace Froq\PortalBundle\Twig\Components\Button;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'IconButton', template: '@FroqPortal/components/button/IconButton.html.twig')]
final class IconButton
{
    public string $icon;
    public bool $active = false;
    public string $label;
} 