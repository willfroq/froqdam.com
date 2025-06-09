<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components;

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Data\ElementMetadata;
use Pimcore\Model\DataObject\PrintGuideline;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'PrintGuidelineSection', template: '@FroqPortal/components/PrintGuidelineSection.html.twig')]
final class PrintGuidelineSection
{
    public PrintGuideline $printGuideline;

    /** @var array<int, Asset> */
    public array $attachmentFiles;

    public function mount(PrintGuideline $printGuideline): void
    {
        foreach ($printGuideline->getAttachments() as $attachment) {
            if (!($attachment instanceof ElementMetadata)) {
                continue;
            }

            $asset = Asset::getById((int) $attachment->getElementId());

            if (!($asset instanceof Asset)) {
                continue;
            }

            $this->attachmentFiles[] = $asset;
        }
    }
}
