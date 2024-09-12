<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Modal;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ShareModal', template: '@FroqPortal/components/modal/share-modal.html.twig')]
final class ShareModal
{
    public string $url;
}
