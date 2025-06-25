<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Layout;

use Froq\PortalBundle\Opensearch\ValueObject\SidebarFilter;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Sidebar', template: '@FroqPortal/components/layout/Sidebar.html.twig')]
final class Sidebar
{
    /** @var array<int, SidebarFilter> */
    public array $sidebarFilters = [];

    public string $homeUrl;

    public bool $hasSelectedFilters = false;
}
