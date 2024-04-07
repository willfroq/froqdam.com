<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ESPropertyMapping;

use Froq\PortalBundle\ESPropertyMapping\Traits\NestedFieldMapperTrait;
use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Froq\PortalBundle\Helper\StrHelper;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\AssetResource;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;
use Youwe\PimcoreElasticsearchBundle\Mapping\Property\ConfigurationAwarePropertyMappingInterface;
use Youwe\PimcoreElasticsearchBundle\Mapping\Property\ConfigurationAwarePropertyMappingTrait;
use Youwe\PimcoreElasticsearchBundle\Mapping\Property\DefinitionAwarePropertyMappingInterface;
use Youwe\PimcoreElasticsearchBundle\Mapping\Property\DefinitionAwarePropertyMappingTrait;
use Youwe\PimcoreElasticsearchBundle\Mapping\Property\PropertyMappingInterface;
use Youwe\PimcoreElasticsearchBundle\Mapping\Property\PropertyNameAwarePropertyMappingInterface;
use Youwe\PimcoreElasticsearchBundle\Mapping\Property\PropertyNameAwarePropertyMappingTrait;

class PdfTextMapper implements
    PropertyMappingInterface,
    ConfigurationAwarePropertyMappingInterface,
    PropertyNameAwarePropertyMappingInterface,
    DefinitionAwarePropertyMappingInterface
{
    use NestedFieldMapperTrait;
    use ConfigurationAwarePropertyMappingTrait;
    use PropertyNameAwarePropertyMappingTrait;
    use DefinitionAwarePropertyMappingTrait {
        getDefinition as protected getConfiguredDefinition;
    }

    private const CONFIG_NESTED_FIELD = 'nested_field';
    private const CONFIG_FROM_LATEST_VERSION = 'from_latest_version';

    /**
     * @return array<string, mixed>
     */
    public function getDefinition(): array
    {
        return $this->getConfiguredDefinition() ?: ['type' => MappingTypes::MAPPING_TYPE_TEXT];
    }

    /**
     * @param object $element
     *
     * @return bool|int|float|string|array<string|int, mixed>|null
     */
    public function translate(object $element): bool|int|float|string|array|null
    {
        $this->resolveOptions($this->configuration);

        if (!$element instanceof AbstractObject) {
            return null;
        }

        if (!($element instanceof AssetResource)) {
            return null;
        }

        if ($this->getConfiguration(self::CONFIG_FROM_LATEST_VERSION) === true) {
            $element = AssetResourceHierarchyHelper::getLatestVersion($element);
        }

        $assets = $this->getNestedFieldValues(
            $element,
            $this->propertyName,
            explode('.', $this->getConfiguration(self::CONFIG_NESTED_FIELD))
        );

        $values = [];
        /** @var Asset&Asset\Document $asset */
        foreach ($assets ?? [] as $asset) {
            if (!$this->isMappablePdfAsset($asset)) {
                continue;
            }

            /** @var string $pdfText */
            $pdfText = StrHelper::hardTrim(
                $asset->getText(),
                regexReplacements: [
                    '/[\x{200B}-\x{200D}\x{FEFF}]/u' => '', // Remove Unicode Zero Width
                ]
            );

            $pdfTextLines = explode("\r\n", $pdfText);

            foreach ($pdfTextLines as $pdfTextLine) {
                $pdfTextLine = StrHelper::hardTrim(
                    $pdfTextLine,
                    regexReplacements: [
                        '/\s+/' => ' ', // replace multiple spaces to 1 space
                    ]
                );

                if ($pdfText) {
                    $values[] = $pdfTextLine;
                }
            }
        }

        return $values;
    }

    private function isMappablePdfAsset(?Asset $asset): bool
    {
        return $asset && ($asset->getMimeType() === 'application/pdf') && method_exists($asset, 'getText') && $asset->getText();
    }

    /**
     * @param array<string|int, mixed> $data
     *
     * @return array<string|int, mixed>
     */
    private function resolveOptions(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setDefault(self::CONFIG_FROM_LATEST_VERSION, false)
            ->setAllowedTypes(self::CONFIG_FROM_LATEST_VERSION, 'bool')
            ->setRequired([self::CONFIG_NESTED_FIELD])
            ->setAllowedTypes(self::CONFIG_NESTED_FIELD, 'string')
            ->setAllowedValues(self::CONFIG_NESTED_FIELD, [
                Validation::createIsValidCallable(new NotBlank()),
            ]);

        return $resolver->resolve($data);
    }
}
