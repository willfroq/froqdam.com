<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components;

use Pimcore\Model\DataObject\Medium;
use Pimcore\Model\DataObject\PrintGuideline;
use Pimcore\Model\DataObject\PrintingTechnique;
use Pimcore\Model\DataObject\Substrate;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'PrintGuidelineSection', template: '@FroqPortal/components/PrintGuidelineSection.html.twig')]
final class PrintGuidelineSection
{
    public PrintGuideline $printGuideline;

    /** @var array<int, Medium> */
    public array $mediums;

    /** @var array<int, Substrate> */
    public array $substrates;

    /** @var array<int, PrintingTechnique> */
    public array $printingTechniques;

    /** @var array<int, Medium|Substrate|PrintingTechnique> */
    public array $requiredSpecs;

    public function mount(PrintGuideline $printGuideline): void
    {
        $ids = explode('-', (string) $printGuideline->getCompositeIds());

        $specs = [];

        foreach ($ids as $key => $id) {
            $specs[$key] = (function () use ($key, $id) {
                return match($key) {
                    0 => Medium::getById($id),
                    1 => Substrate::getById($id),
                    2 => PrintingTechnique::getById($id),

                    default => null
                };
            })();
        }

        $this->requiredSpecs = array_filter($specs);
    }
}
