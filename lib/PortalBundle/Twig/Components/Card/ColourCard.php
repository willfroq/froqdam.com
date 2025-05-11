<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Card;

use Pimcore\Model\DataObject\Colour;
use Pimcore\Model\DataObject\Fieldcollection\Data\ColourFieldCollection;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'ColourCard', template: '@FroqPortal/components/card/ColourCard.html.twig')]
final class ColourCard
{
    public Colour $colour;

    /** @var array<int, array<string, string>> */
    public array $specs = [];

    public function mount(Colour $colour): void
    {
        $colourFieldCollections = $colour->getColourFieldCollection()?->getItems();

        foreach ($colourFieldCollections ?? [] as $colourFieldCollection) {
            if (!($colourFieldCollection instanceof ColourFieldCollection)) {
                continue;
            }

            $this->specs[] = [
                'key' => (string) $colourFieldCollection->getColourKey(),
                'value' => (string) $colourFieldCollection->getColourValue(),
            ];
        }
    }
}
