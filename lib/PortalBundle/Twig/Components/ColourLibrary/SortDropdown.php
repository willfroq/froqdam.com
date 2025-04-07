<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\ColourLibrary;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'SortDropdown', template: '@FroqPortal/components/colour-library/SortDropdown.html.twig')]
final class SortDropdown
{
    /** @var array<string, string> */
    public array $options = [
        'Name (ASC)' => 'name_asc',
        'Name (DESC)' => 'name_desc',
        'Date (ASC)' => 'date_asc',
        'Date (DESC)' => 'date_desc',
    ];
}