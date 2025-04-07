<?php

namespace Froq\PortalBundle\Twig\Components\Button;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Button', template: '@FroqPortal/components/button/Button.html.twig')]
final class Button
{
    public string $variant = 'primary';
    public string $size = 'md';
    public bool $disabled = false;
    public bool $hasIcon = false;
    public string $iconPosition = 'left';
} 