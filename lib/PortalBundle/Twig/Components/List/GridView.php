<?php

namespace Froq\PortalBundle\Twig\Components\List;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'GridView', template: '@FroqPortal/components/list/GridView.html.twig')]
final class GridView
{
    public array $items = [];
    public string $class = '';
} 