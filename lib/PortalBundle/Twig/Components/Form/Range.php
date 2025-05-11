<?php

namespace Froq\PortalBundle\Twig\Components\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Range', template: '@FroqPortal/components/form/Range.html.twig')]
final class Range
{
    public string $label;
    public ?string $minValue = null;
    public ?string $maxValue = null;
    public string $class = '';
}
