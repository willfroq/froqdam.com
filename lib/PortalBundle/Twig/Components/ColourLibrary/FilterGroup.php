<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\ColourLibrary;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'FilterGroup', template: '@FroqPortal/components/colour-library/FilterGroup.html.twig')]
final class FilterGroup
{
    public string $title;
    public array $itemTypes = [];
    public bool $isOpen = true;
    public string $searchPlaceholder = '';
    public string $itemCount = '8';
} 