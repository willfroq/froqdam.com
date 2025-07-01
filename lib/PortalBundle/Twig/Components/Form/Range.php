<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Range', template: '@FroqPortal/components/form/Range.html.twig')]
final class Range
{
    public string $label;

    public string $filterName;

    public ?int $minValue = null;

    public ?int $maxValue = null;

    public string $class = '';
}
