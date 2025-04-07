<?php

namespace Froq\PortalBundle\Twig\Components\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'SearchInput', template: '@FroqPortal/components/form/SearchInput.html.twig')]
final class SearchInput
{
    public ?string $value = null;
    public bool $showClearButton = false;
    public string $class = '';
} 