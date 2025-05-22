<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'SearchSidebar', template: '@FroqPortal/components/form/SearchSidebar.html.twig')]
final class SearchSidebar
{
    public string $placeholder = 'Search';

    public string $size = 'default'; // default, sm

    public string $filterName = '';
}
