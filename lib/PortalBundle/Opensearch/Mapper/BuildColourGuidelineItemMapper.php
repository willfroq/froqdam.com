<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper;

use Froq\PortalBundle\Opensearch\Action\GetBrandNames;
use Froq\PortalBundle\Opensearch\Action\GetMarketNames;
use Froq\PortalBundle\Opensearch\Action\GetYamlConfigFileProperties;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Froq\PortalBundle\Opensearch\Exception\MappingDoesNotMatchException;
use Froq\PortalBundle\Opensearch\Utility\FlattenArray;
use Pimcore\Model\DataObject\Colour;
use Pimcore\Model\DataObject\ColourGuideline;
use Pimcore\Model\DataObject\Fieldcollection\Data\ColourFieldCollection;
use Pimcore\Model\DataObject\PrintGuideline;
use Pimcore\Model\DataObject\Product;

final class BuildColourGuidelineItemMapper
{
    public function __construct(
        private readonly GetYamlConfigFileProperties $getYamlConfigFileProperties,
        private readonly FlattenArray $flattenArray,
        private readonly GetBrandNames $getBrandNames,
        private readonly GetMarketNames $getMarketNames,
    ) {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Exception
     */
    public function __invoke(ColourGuideline $colourGuideline): array
    {
        $mapping = ($this->getYamlConfigFileProperties)(IndexNames::ColourGuidelineItem->readable());

        $colours = $colourGuideline->getColours();

        $printGuidelines = $colourGuideline->getPrintGuidelines();

        /** @var Product[] $products */
        $products = (array) $colourGuideline->getCategory()?->getProducts();

        $mappedColourGuidelineItem = [
            // Will be denormalized into ColourGuidelineItem DTO
            'colourGuidelineId' => (int) $colourGuideline->getId(),
            'name' => (string) $colourGuideline->getName(),
            'imageId' => (int) $colourGuideline->getImage()?->getId(),
            'countries' => (array) $colourGuideline->getCategory()?->getMarkets(),

            'created_at_timestamp' => (int) $colourGuideline->getCreationDate(),
            'updated_at_timestamp' => (int) $colourGuideline->getModificationDate(),
            'description' => (string) $colourGuideline->getDescription(),

            // Aggregate Filters
            'brands' => ($this->getBrandNames)($colourGuideline),
            'markets' => ($this->getMarketNames)($colourGuideline),

            // Relations
            'organization_id' => (int) $colourGuideline->getOrganization()?->getId(),
            'organization_name' => (string) $colourGuideline->getOrganization()?->getKey(),

            'image_id' => (int) $colourGuideline->getImage()?->getId(),
            'image_filename' => (string) $colourGuideline->getImage()?->getFilename(),

            'category_id' => (int) $colourGuideline->getCategory()?->getId(),
            'category_reporting_type' => (string) $colourGuideline->getCategory()?->getReportingType(),
            'category_level_label' => (string) $colourGuideline->getCategory()?->getLevelLabel(),

            'product_ids' => array_values(array_filter(array_map(fn (Product $product) => $product->getId(), $products))),
            'product_names' => array_values(array_filter(array_map(fn (Product $product) => $product->getName(), $products))),

            'colour_ids' => array_values(array_filter(array_map(fn (Colour $colour) => $colour->getId(), $colours))),
            'colour_names' => array_values(array_filter(array_map(fn (Colour $colour) => $colour->getName(), $colours))),
            'colour_fields_keys' => ($this->flattenArray)(
                array_values(array_filter(array_map(
                    fn (Colour $colour) => array_values(array_filter(array_map(
                        function (mixed $colourFieldCollection) {
                            if ($colourFieldCollection instanceof ColourFieldCollection) {
                                return $colourFieldCollection->getColourKey();
                            }

                            return null;
                        },
                        (array) $colour->getColourFieldCollection()?->getItems()))),
                    $colours
                )))
            ),
            'colour_fields_values' => ($this->flattenArray)(
                array_values(array_filter(array_map(
                    fn (Colour $colour) => array_values(array_filter(array_map(
                        function (mixed $colourFieldCollection) {
                            if ($colourFieldCollection instanceof ColourFieldCollection) {
                                return $colourFieldCollection->getColourValue();
                            }

                            return null;
                        },
                        (array) $colour->getColourFieldCollection()?->getItems()))),
                    $colours
                )))
            ),

            'print_guidelines_ids' => array_values(array_filter(array_map(fn (PrintGuideline $printGuideline) => $printGuideline->getId(), $printGuidelines))),
            'print_guidelines_names' => array_values(array_filter(array_map(fn (PrintGuideline $printGuideline) => $printGuideline->getName(), $printGuidelines))),
            'print_guidelines_descriptions' => array_values(array_filter(array_map(fn (PrintGuideline $printGuideline) => $printGuideline->getDescription(), $printGuidelines))),
            'print_guidelines_medium_names' => array_values(array_filter(array_map(fn (PrintGuideline $printGuideline) => $printGuideline->getMedium()?->getName(), $printGuidelines))),
            'print_guidelines_substrate_names' => array_values(array_filter(array_map(fn (PrintGuideline $printGuideline) => $printGuideline->getSubstrate()?->getName(), $printGuidelines))),
            'print_guidelines_printing_technique_names' => array_values(array_filter(array_map(fn (PrintGuideline $printGuideline) => $printGuideline->getPrintingTechnique()?->getName(), $printGuidelines))),
        ];

        if (!empty(array_diff(array_keys($mappedColourGuidelineItem), array_keys($mapping)))) {
            throw MappingDoesNotMatchException::mismatch($mappedColourGuidelineItem, $mapping);
        }

        return $mappedColourGuidelineItem;
    }
}
