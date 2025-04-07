<?php

namespace Froq\PortalBundle\Twig\Components\Navigation;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'SortSelect', template: '@FroqPortal/components/navigation/SortSelect.html.twig')]
final class SortSelect
{
    public string $label;
    public array $items = [];
    public ?string $selected = null;
    public string $class = '';
    public ?string $name = null;
} 