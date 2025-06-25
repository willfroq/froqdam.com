<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Layout;

use Froq\PortalBundle\Opensearch\ValueObject\SidebarFilter;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'FilterGroup', template: '@FroqPortal/components/layout/FilterGroup.html.twig')]
final class FilterGroup
{
    public SidebarFilter $sidebarFilter;
}
