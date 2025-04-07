<?php

namespace Froq\PortalBundle\Twig\Components\Layout;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Sidebar', template: '@FroqPortal/components/layout/Sidebar.html.twig')]
final class Sidebar
{
    public array $filters = [];
    public ?string $width = 'w-[280px]';
    public ?string $class = '';
} 