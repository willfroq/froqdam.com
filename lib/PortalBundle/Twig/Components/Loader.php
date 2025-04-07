<?php

namespace Froq\PortalBundle\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Loader', template: '@FroqPortal/components/Loader.html.twig')]
final class Loader
{
    public string $size = 'md';
    public string $color = 'blue';
    public string $class = '';
} 