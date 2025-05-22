<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'SearchBar', template: '@FroqPortal/components/form/SearchBar.html.twig')]
final class SearchBar
{
    public string $placeholder = 'Search';
}
