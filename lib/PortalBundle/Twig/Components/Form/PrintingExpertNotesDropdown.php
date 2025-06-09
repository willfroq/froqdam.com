<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Form;

use Froq\PortalBundle\Opensearch\ValueObject\SortOption;
use Pimcore\Model\DataObject\PrintGuideline;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'PrintingExpertNotesDropdown', template: '@FroqPortal/components/form/PrintingExpertNotesDropdown.html.twig')]
final class PrintingExpertNotesDropdown
{
    public string $id = '';

    /** @var array<int, PrintGuideline> */
    public array $printGuidelines;

    public string $keyLabel = '';

    /** @var array<int, SortOption> */
    public array $sortOptions = [];

    public ?SortOption $selectedSortOption = null;

    public ?string $width = null;

    /** @param array<int, PrintGuideline> $printGuidelines */
    public function mount(array $printGuidelines): void
    {
        foreach ($printGuidelines as $printGuideline) {
            $mediumName = $printGuideline->getMedium()?->getName();
            $mediumTypeName = $printGuideline->getMediumType()?->getName();
            $substrateName = $printGuideline->getSubstrate()?->getName();
            $printingTechniqueName = $printGuideline->getPrintTechnique()?->getName();

            $this->sortOptions[] = new SortOption(
                label: "$mediumName-$mediumTypeName-$substrateName-$printingTechniqueName",
                filterName: '',
                sortDirection: '',
            );
        }

        $option = current($this->sortOptions);

        if (!($option instanceof SortOption)) {
            return;
        }

        $this->selectedSortOption = $option;
    }
}
