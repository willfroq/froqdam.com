<?php

namespace Froq\PortalBundle\Twig\Components\Layout;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'FilterGroup', template: '@FroqPortal/components/layout/FilterGroup.html.twig')]
final class FilterGroup
{
    public string $title;
    /** @var array<string> */
    public array $items = [];
    public ?string $selectedItem = null;
    public bool $expanded = false;
}
