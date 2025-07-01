<?php

namespace Froq\PortalBundle\Twig\Components\Card;

use Froq\PortalBundle\Opensearch\ValueObject\Column;
use Pimcore\Model\Asset;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'AssetGridCard', template: '@FroqPortal/components/card/AssetGridCard.html.twig')]
final class AssetGridCard
{
    public int $id;

    public Asset $asset;

    public string $name = '';

    public ?string $imagePath = null;

    public ?string $detailPath = null;

    /** @var array<int, Column> */
    public array $columns;
}
