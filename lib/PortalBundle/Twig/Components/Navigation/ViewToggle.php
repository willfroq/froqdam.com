<?php

namespace Froq\PortalBundle\Twig\Components\Navigation;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ViewToggle', template: '@FroqPortal/components/navigation/ViewToggle.html.twig')]
final class ViewToggle
{
    public string $view = 'grid';
    public string $class = '';
}
