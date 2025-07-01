<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper;

use Froq\PortalBundle\Opensearch\Action\GetCategoryNames;
use Froq\PortalBundle\Opensearch\Action\GetYamlConfigFileProperties;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Froq\PortalBundle\Opensearch\Exception\MappingDoesNotMatchException;
use Pimcore\Model\DataObject\ColourDefinition;
use Pimcore\Model\DataObject\ColourGuideline;
use Pimcore\Model\DataObject\PrintGuideline;
use Psr\Cache\InvalidArgumentException;

final class BuildColourGuidelineItemMapper
{
    public function __construct(
        private readonly GetYamlConfigFileProperties $getYamlConfigFileProperties,
        private readonly GetCategoryNames $getCategoryNames,
    ) {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Exception
     * @throws InvalidArgumentException
     */
    public function __invoke(ColourGuideline $colourGuideline): array
    {
        $mapping = ($this->getYamlConfigFileProperties)(IndexNames::ColourGuidelineItem->readable());

        $colours = $colourGuideline->getColours();

        $printGuidelines = $colourGuideline->getPrintGuidelines();

        $mappedColourGuidelineItem = [
            // Will be denormalized into ColourGuidelineItem DTO
            'colourGuidelineId' => (int) $colourGuideline->getId(),
            'name' => (string) $colourGuideline->getName(),
            'imageId' => (int) $colourGuideline->getImage()?->getId(),
            'countries' => ($this->getCategoryNames)($colourGuideline, 'markets'),

            'created_at_timestamp' => (int) $colourGuideline->getCreationDate(),
            'updated_at_timestamp' => (int) $colourGuideline->getModificationDate(),
            'description' => (string) $colourGuideline->getDescription(),

//            // Aggregate Filters
//            // keyword
            'brands' => ($this->getCategoryNames)($colourGuideline, 'brands'),
            'markets' => ($this->getCategoryNames)($colourGuideline, 'markets'),
            'campaigns' => ($this->getCategoryNames)($colourGuideline, 'campaigns'),
            'mediums' => array_values(array_unique(array_map(
                fn (PrintGuideline $printGuideline) => $printGuideline->getMedium()?->getName(),
                $colourGuideline->getPrintGuidelines()
            ))),
            'substrates' => array_values(array_unique(array_map(
                fn (PrintGuideline $printGuideline) => $printGuideline->getSubstrate()?->getName(),
                $colourGuideline->getPrintGuidelines()
            ))),
            'medium_types' => array_values(array_unique(array_map(
                fn (PrintGuideline $printGuideline) => $printGuideline->getMediumType()?->getName(),
                $colourGuideline->getPrintGuidelines()
            ))),
            'printing_techniques' => array_values(array_unique(array_map(
                fn (PrintGuideline $printGuideline) => $printGuideline->getPrintTechnique()?->getName(),
                $colourGuideline->getPrintGuidelines()
            ))),

            // Relations
            'organization_id' => (int) $colourGuideline->getOrganization()?->getId(),
            'organizations' => (string) $colourGuideline->getOrganization()?->getKey(),

            'image_id' => (int) $colourGuideline->getImage()?->getId(),
            'image_filename' => (string) $colourGuideline->getImage()?->getFilename(),

            'colour_ids' => array_values(array_unique(array_map(fn (ColourDefinition $colour) => $colour->getId(), $colours))),
            'colour_names' => array_values(array_unique(array_map(fn (ColourDefinition $colour) => $colour->getName(), $colours))),

            'print_guidelines_ids' => array_values(array_unique(array_map(fn (PrintGuideline $printGuideline) => $printGuideline->getId(), $printGuidelines))),
            'print_guidelines_names' => array_values(array_unique(array_map(fn (PrintGuideline $printGuideline) => $printGuideline->getKey(), $printGuidelines))),
            'print_guidelines_descriptions' => array_values(array_unique(array_map(fn (PrintGuideline $printGuideline) => $printGuideline->getDescription(), $printGuidelines))),
        ];

        if (!empty(array_diff(array_keys($mappedColourGuidelineItem), array_keys($mapping)))) {
            throw MappingDoesNotMatchException::mismatch($mappedColourGuidelineItem, $mapping);
        }

        return $mappedColourGuidelineItem;
    }
}
