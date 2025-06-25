<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Card;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'AssetResourceTableRow', template: '@FroqPortal/components/card/AssetResourceTableRow.html.twig')]
final class AssetResourceTableRow
{
    public int $id;

    public string $name = '';

    public ?string $imagePath = null;

    public ?string $detailPath = null;
}
