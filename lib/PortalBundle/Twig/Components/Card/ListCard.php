<?php

namespace Froq\PortalBundle\Twig\Components\Card;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ListCard', template: '@FroqPortal/components/card/ListCard.html.twig')]
final class ListCard
{
    public string $title;
    public string $markets;
    public string $imageUrl;
    public int $id;
} 