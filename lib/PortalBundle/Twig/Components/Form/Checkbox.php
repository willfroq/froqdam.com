<?php

namespace Froq\PortalBundle\Twig\Components\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Checkbox', template: '@FroqPortal/components/form/Checkbox.html.twig')]
final class Checkbox
{
    public string $label = '';
    public bool $checked = false;
}
