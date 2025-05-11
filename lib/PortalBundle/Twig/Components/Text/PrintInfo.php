<?php

namespace Froq\PortalBundle\Twig\Components\Text;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'PrintInfo', template: '@FroqPortal/components/text/PrintInfo.html.twig')]
final class PrintInfo
{
    public string $description = '';
}
