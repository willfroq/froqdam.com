<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Layout;

use Froq\PortalBundle\Opensearch\ValueObject\Aggregation;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Sidebar', template: '@FroqPortal/components/layout/Sidebar.html.twig')]
final class Sidebar
{
    /** @var array<int, string> */
    public array $filters = [];

    public ?string $width = 'w-[280px]';

    public ?string $class = '';

    /** @var array<int, Aggregation> */
    public array $aggregations = [];

    public bool $hasSelectedFilters = false;
}
