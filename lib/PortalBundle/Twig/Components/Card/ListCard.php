<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Card;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ListCard', template: '@FroqPortal/components/card/ListCard.html.twig')]
final class ListCard
{
    public string $name = '';
    /** @var array<string> */
    public array $markets = [];
    public ?string $imagePath = null;
}
