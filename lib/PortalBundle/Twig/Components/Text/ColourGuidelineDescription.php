<?php

namespace Froq\PortalBundle\Twig\Components\Text;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ColourGuidelineDescription', template: '@FroqPortal/components/text/ColourGuidelineDescription.html.twig')]
final class ColourGuidelineDescription
{
    public string $description = '';
}
