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

    /** @var array<int, Medium > */
    public array $mediums;

    /** @var array<int, Substrate > */
    public array $substrates;

    /** @var array<int, PrintingTechnique > */
    public array $printingTechniques;
}
