<?php

namespace Froq\PortalBundle\Twig\Components\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Calendar', template: '@FroqPortal/components/form/Calendar.html.twig')]
final class Calendar
{
    public string $label;
    public ?string $fromDate = null;
    public ?string $toDate = null;
    public string $class = '';
}