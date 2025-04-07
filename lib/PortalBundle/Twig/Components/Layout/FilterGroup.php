<?php

namespace Froq\PortalBundle\Twig\Components\Layout;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'FilterGroup', template: '@FroqPortal/components/layout/FilterGroup.html.twig')]
final class FilterGroup
{
    public string $title;
    public array $items = [];
    public bool $isOpen = true;
    public ?string $searchPlaceholder = null;
    public array $selected = [];
} 