<?php

namespace Froq\PortalBundle\Twig\Components\Media;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Icon', template: '@FroqPortal/components/media/Icon.html.twig')]
final class Icon
{
    public string $name;
    public string $size = 'md';
    public string $class = '';
}
