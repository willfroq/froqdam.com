<?php

namespace Froq\PortalBundle\Twig\Components\Button;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('ViewAllButton')]
final class ViewAllButton
{
    public int $count = 0;
}
