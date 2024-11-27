<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Tooltip', template: '@FroqPortal/components/Tooltip.html.twig')]
final class Tooltip
{
    public string $label;
}
