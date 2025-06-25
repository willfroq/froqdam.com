<?php

namespace Froq\PortalBundle\Twig\Components\Form;

use Froq\PortalBundle\Opensearch\ValueObject\SidebarFilter;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Accordion', template: '@FroqPortal/components/form/Accordion.html.twig')]
final class Accordion
{
    public string $id;

    public string $title;

    public bool $expanded = false;

    public SidebarFilter $sidebarFilter;
}
