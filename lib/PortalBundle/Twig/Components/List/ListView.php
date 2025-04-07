<?php

namespace Froq\PortalBundle\Twig\Components\List;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ListView', template: '@FroqPortal/components/list/ListView.html.twig')]
final class ListView
{
    public array $items = [];
    public string $class = '';
} 