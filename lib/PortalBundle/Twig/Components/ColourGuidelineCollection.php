<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components;

use Froq\PortalBundle\ColourLibrary\DataTransferObject\ColourGuidelineItem;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ColourGuidelineCollection', template: '@FroqPortal/components/ColourGuidelineCollection.html.twig')]
final class ColourGuidelineCollection
{
    /** @var array<int, ColourGuidelineItem > */
    public array $colourGuidelineItems = [];

    public string $currentView = 'grid';

}
