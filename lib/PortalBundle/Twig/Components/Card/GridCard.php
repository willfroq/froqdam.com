<?php

namespace Froq\PortalBundle\Twig\Components\Card;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'GridCard', template: '@FroqPortal/components/card/GridCard.html.twig')]
final class GridCard
{
    public string $name = '';

    /** @var array<int, string> */
    public array $markets = [];

    public ?string $imagePath = null;

    public int $id;
}
