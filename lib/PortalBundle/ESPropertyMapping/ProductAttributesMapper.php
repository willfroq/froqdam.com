<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ESPropertyMapping;

use Froq\PortalBundle\ESPropertyMapping\Traits\NestedFieldMapperTrait;
use Froq\PortalBundle\Exception\ES\ESPropertyMappingException;
use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Fieldcollection\Data\ProductAttributes;
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

class ProductAttributesMapper extends AbstractMapper implements
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

    private const CONFIG_NESTED_FC_FIELD = 'nested_fc_field';
    private const CONFIG_FIELD_COLLECTION_KEY = 'field_collection_key';
    private const CONFIG_FROM_LATEST_VERSION = 'from_latest_version';

    /**
     * @return array<string|int, mixed>
     */
    public function getDefinition(): array
    {
        return $this->getConfiguredDefinition() ?: ['type' => 'keyword'];
    }

    /**
     * @param object $element
     *
     * @return bool|int|float|string|array<string|int, mixed>|null
     */
    public function translate(object $element): bool|int|float|string|array|null
    {
        try {
            $this->resolveOptions($this->configuration);

            if (!$element instanceof AssetResource) {
                return null;
            }

            if ($this->getConfiguration(self::CONFIG_FROM_LATEST_VERSION) === true) {
                $element = AssetResourceHierarchyHelper::getLatestVersion($element);
            }

            $fieldCollections = $this->getNestedFieldValues(
                $element,
                $this->propertyName,
                explode('.', $this->getConfiguration(self::CONFIG_NESTED_FC_FIELD))
            );

            $values = [];

            foreach ($fieldCollections ?? [] as $fc) {
                if (!($fc instanceof Fieldcollection)) {
                    continue;
                }

                if ($value = $this->getAttributeValue($fc)) {
                    $values[] = $value;
                }
            }

            return array_unique($values);
        } catch (\Exception $exception) {
            $this->logger->error(sprintf(
                '%s: %s',
                ESPropertyMappingException::PROPERTY_MAPPING_EXCEPTION,
                $exception->getMessage()
            ));
        }

        return null;
    }

    private function getAttributeValue(Fieldcollection $fc): ?string
    {
        /** @var ProductAttributes $attributes */
        foreach ($fc->getItems() as $attributes) {
            if ($attributes->getAttributeKey() === $this->getConfiguration(self::CONFIG_FIELD_COLLECTION_KEY)) {
                return $attributes->getAttributeValue();
            }
        }

        return null;
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
            ->setRequired([self::CONFIG_NESTED_FC_FIELD, self::CONFIG_FIELD_COLLECTION_KEY])
            ->setAllowedTypes(self::CONFIG_NESTED_FC_FIELD, 'string')
            ->setAllowedValues(self::CONFIG_NESTED_FC_FIELD, [
                Validation::createIsValidCallable(new NotBlank()),
            ])
            ->setAllowedTypes(self::CONFIG_FIELD_COLLECTION_KEY, 'string')
            ->setAllowedValues(self::CONFIG_FIELD_COLLECTION_KEY, [
                Validation::createIsValidCallable(new NotBlank()),
            ]);

        return $resolver->resolve($data);
    }
}
