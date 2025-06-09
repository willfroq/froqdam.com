<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Card;

use Pimcore\Model\DataObject\ColourDefinition;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ColourCard', template: '@FroqPortal/components/card/ColourCard.html.twig')]
final class ColourCard
{
    public ColourDefinition $colour;
}
