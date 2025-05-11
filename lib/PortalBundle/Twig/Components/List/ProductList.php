<?php

namespace Froq\PortalBundle\Twig\Components\List;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ProductList', template: '@FroqPortal/components/list/ProductList.html.twig')]
final class ProductList
{
    /** @var array<array{id: int, name: string, imagePath: ?string, markets: array<string>}> */
    public array $items = [];
    public string $currentView = 'grid';
}
