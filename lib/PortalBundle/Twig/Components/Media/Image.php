<?php

namespace Froq\PortalBundle\Twig\Components\Media;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Image', template: '@FroqPortal/components/media/Image.html.twig')]
final class Image
{
    public string $src;
    public ?string $alt = null;
    public string $size = 'md';
    public string $objectFit = 'contain';
    public string $class = '';
}
