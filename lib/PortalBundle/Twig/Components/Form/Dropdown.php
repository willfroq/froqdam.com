<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Form;

use Froq\PortalBundle\Opensearch\ValueObject\SortOption;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Dropdown', template: '@FroqPortal/components/form/Dropdown.html.twig')]
final class Dropdown
{
    public string $id = '';

    public string $label = '';

    /** @var array<int, SortOption> */
    public array $sortOptions = [];

    public ?SortOption $selectedSortOption = null;

    public ?string $width = null;

    public string $placeholder = 'Search...';
    public string $searchUrl = '';
}
