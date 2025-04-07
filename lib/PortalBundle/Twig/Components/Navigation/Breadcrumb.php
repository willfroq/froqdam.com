<?php

namespace Froq\PortalBundle\Twig\Components\Navigation;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Breadcrumb', template: '@FroqPortal/components/navigation/Breadcrumb.html.twig')]
final class Breadcrumb
{
    public array $items = [];
} 