<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Card;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ColourGuidelineTableRow', template: '@FroqPortal/components/card/ColourGuidelineTableRow.html.twig')]
final class ColourGuidelineTableRow
{
    public int $id;

    public string $name = '';

    /** @var array<string> */
    public array $markets = [];

    public ?string $imagePath = null;

    public int $colourCount = 0;

    public ?string $detailPath = null;
}
