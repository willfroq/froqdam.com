<?php

namespace Froq\PortalBundle\Twig\Components\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Input', template: '@FroqPortal/components/form/Input.html.twig')]
final class Input
{
    public string $type = 'text';
    public string $name;
    public string $label;
    public ?string $value = null;
    public ?string $placeholder = null;
    public ?string $errorMessage = null;
    public bool $required = false;
    public string $class = '';
} 