<?php

namespace Froq\PortalBundle\Twig\Components\Card;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'GridCard', template: '@FroqPortal/components/card/GridCard.html.twig')]
final class GridCard
{
    public string $title;
    public string $markets;
    public string $imageUrl;
    public int $id;
} 