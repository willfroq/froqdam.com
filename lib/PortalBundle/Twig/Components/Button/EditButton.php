<?php

namespace Froq\PortalBundle\Twig\Components\Button;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'EditButton', template: '@FroqPortal/components/button/EditButton.html.twig')]
final class EditButton
{
    public ?string $href = '#';
}
