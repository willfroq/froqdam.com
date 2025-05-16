<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Layout;

use Froq\PortalBundle\ColourLibrary\DataTransferObject\Aggregation;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Sidebar', template: '@FroqPortal/components/layout/Sidebar.html.twig')]
final class Sidebar
{
    /** @var array<int, string> */
    public array $filters = [];

    public ?string $width = 'w-[280px]';

    public ?string $class = '';

    /** @var array<int, string> */
    public array $brands = [];

    /** @var array<int, string> */
    public array $markets = [];

    /** @var array<int, string> */
    public array $mediums = [];

    /** @var array<int, Aggregation> */
    public array $aggregations = [];

    public ?string $selectedBrand = null;
}
