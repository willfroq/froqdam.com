<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components;

use Froq\PortalBundle\AssetLibrary\DataTransferObject\AssetResourceItem;
use Froq\PortalBundle\Opensearch\ValueObject\Column;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'AssetResourceCollection', template: '@FroqPortal/components/AssetResourceCollection.html.twig')]
final class AssetResourceCollection
{
    /** @var array<int, AssetResourceItem > */
    public array $colourGuidelineItems = [];

    public string $currentView = 'grid';

    /** @var array<int, Column> */
    public array $columns;

    public bool $isHeaderHidden;
}
