<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Mapper;

use Froq\PortalBundle\Opensearch\Action\GetCategoryNames;
use Froq\PortalBundle\Opensearch\Action\GetYamlConfigFileProperties;
use Froq\PortalBundle\Opensearch\Enum\IndexNames;
use Froq\PortalBundle\Opensearch\Exception\MappingDoesNotMatchException;
use Froq\PortalBundle\Opensearch\Utility\FlattenArray;
use Pimcore\Model\DataObject\Category;
use Pimcore\Model\DataObject\Colour;
use Pimcore\Model\DataObject\ColourGuideline;
use Pimcore\Model\DataObject\Fieldcollection\Data\ColourFieldCollection;
use Pimcore\Model\DataObject\Medium;
use Pimcore\Model\DataObject\Organization;
use Pimcore\Model\DataObject\PrintGuideline;
use Pimcore\Model\DataObject\PrintingTechnique;
use Pimcore\Model\DataObject\Substrate;

final class BuildColourGuidelineItemMapper
{
    public function __construct(
        private readonly GetYamlConfigFileProperties $getYamlConfigFileProperties,
        private readonly FlattenArray $flattenArray,
        private readonly GetCategoryNames $getCategoryNames,
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

        $categories = $colourGuideline->getCategories();

        $mappedColourGuidelineItem = [
            // Will be denormalized into ColourGuidelineItem DTO
            'colourGuidelineId' => (int) $colourGuideline->getId(),
            'name' => (string) $colourGuideline->getName(),
            'imageId' => (int) $colourGuideline->getImage()?->getId(),
            'countries' => ($this->getCategoryNames)($colourGuideline, 'market'),

            'created_at_timestamp' => (int) $colourGuideline->getCreationDate(),
            'updated_at_timestamp' => (int) $colourGuideline->getModificationDate(),
            'description' => (string) $colourGuideline->getDescription(),

            // Aggregate Filters
            // keyword
            'brands' => ($this->getCategoryNames)($colourGuideline, 'brand'),
            'markets' => ($this->getCategoryNames)($colourGuideline, 'market'),
            'mediums' => array_values(array_unique(array_map(
                fn (Medium $medium) => $medium->getName(),
                (array) (function () use ($colourGuideline) {
                    $organization = Organization::getById((int) $colourGuideline->getOrganization()?->getId());

                    if (!($organization instanceof Organization)) {
                        return null;
                    }

                    return $organization->getMediums();
                })()
            ))),
            'substrates' => array_values(array_unique(array_map(
                fn (Substrate $substrate) => $substrate->getName(),
                (array) (function () use ($colourGuideline) {
                    $organization = Organization::getById((int) $colourGuideline->getOrganization()?->getId());

                    if (!($organization instanceof Organization)) {
                        return null;
                    }

                    return $organization->getSubstrates();
                })()
            ))),
            'printing_techniques' => array_values(array_unique(array_map(
                fn (PrintingTechnique $printingTechnique) => $printingTechnique->getName(),
                (array) (function () use ($colourGuideline) {
                    $organization = Organization::getById((int) $colourGuideline->getOrganization()?->getId());

                    if (!($organization instanceof Organization)) {
                        return null;
                    }

                    return $organization->getPrintingTechniques();
                })()
            ))),

            // text
            'brands_text' => ($this->getCategoryNames)($colourGuideline, 'brand'),
            'markets_text' => ($this->getCategoryNames)($colourGuideline, 'market'),
            'mediums_text' => array_values(array_unique(array_map(
                fn (Medium $medium) => $medium->getName(),
                (array) (function () use ($colourGuideline) {
                    $organization = Organization::getById((int) $colourGuideline->getOrganization()?->getId());

                    if (!($organization instanceof Organization)) {
                        return null;
                    }

                    return $organization->getMediums();
                })()
            ))),
            'substrates_text' => array_values(array_unique(array_map(
                fn (Substrate $substrate) => $substrate->getName(),
                (array) (function () use ($colourGuideline) {
                    $organization = Organization::getById((int) $colourGuideline->getOrganization()?->getId());

                    if (!($organization instanceof Organization)) {
                        return null;
                    }

                    return $organization->getSubstrates();
                })()
            ))),
            'printing_techniques_text' => array_values(array_unique(array_map(
                fn (PrintingTechnique $printingTechnique) => $printingTechnique->getName(),
                (array) (function () use ($colourGuideline) {
                    $organization = Organization::getById((int) $colourGuideline->getOrganization()?->getId());

                    if (!($organization instanceof Organization)) {
                        return null;
                    }

                    return $organization->getPrintingTechniques();
                })()
            ))),

            // Relations
            'organization_id' => (int) $colourGuideline->getOrganization()?->getId(),
            'organizations' => (string) $colourGuideline->getOrganization()?->getKey(),

            'image_id' => (int) $colourGuideline->getImage()?->getId(),
            'image_filename' => (string) $colourGuideline->getImage()?->getFilename(),

            'category_ids' => array_values(array_unique(array_map(fn (Category $category) => $category->getId(), $categories))),

            'colour_ids' => array_values(array_unique(array_map(fn (Colour $colour) => $colour->getId(), $colours))),
            'colour_names' => array_values(array_unique(array_map(fn (Colour $colour) => $colour->getName(), $colours))),
            'colour_fields_keys' => array_values(array_unique(($this->flattenArray)(
                array_map(
                    fn (Colour $colour) => array_map(
                        function (mixed $colourFieldCollection) {
                            if ($colourFieldCollection instanceof ColourFieldCollection) {
                                return $colourFieldCollection->getColourKey();
                            }

                            return null;
                        },
                        (array) $colour->getColourFieldCollection()?->getItems()),
                    $colours
                )
            ))),
            'colour_fields_values' => array_values(array_unique(($this->flattenArray)(
                array_map(
                    fn (Colour $colour) => array_map(
                        function (mixed $colourFieldCollection) {
                            if ($colourFieldCollection instanceof ColourFieldCollection) {
                                return $colourFieldCollection->getColourValue();
                            }

                            return null;
                        },
                        (array) $colour->getColourFieldCollection()?->getItems()),
                    $colours
                )
            ))),

            'print_guidelines_ids' => array_values(array_unique(array_map(fn (PrintGuideline $printGuideline) => $printGuideline->getId(), $printGuidelines))),
            'print_guidelines_names' => array_values(array_unique(array_map(fn (PrintGuideline $printGuideline) => $printGuideline->getName(), $printGuidelines))),
            'print_guidelines_descriptions' => array_values(array_unique(array_map(fn (PrintGuideline $printGuideline) => $printGuideline->getDescription(), $printGuidelines))),
            'print_guidelines_composite_ids' => array_values(array_unique(array_map(fn (PrintGuideline $printGuideline) => $printGuideline->getCompositeIds(), $printGuidelines))),
        ];

        if (!empty(array_diff(array_keys($mappedColourGuidelineItem), array_keys($mapping)))) {
            throw MappingDoesNotMatchException::mismatch($mappedColourGuidelineItem, $mapping);
        }

        return $mappedColourGuidelineItem;
    }
}
