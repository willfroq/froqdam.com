<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ESPropertyMapping;

use Froq\PortalBundle\Action\ESPropertyMapping\GetProductContentPropertyValues;
use Froq\PortalBundle\ESPropertyMapping\Traits\NestedFieldMapperTrait;
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

class ProductNetUnitContentsMapper implements
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
     * @return array<string|int, mixed>
     */
    public function translate(object $element): array
    {
        $this->resolveOptions($this->configuration);

        if (!($element instanceof AssetResource)) {
            return [];
        }

        $metricUnits = [];

        if (isset($this->configuration[self::CONFIG_FIELD_COLLECTION_KEY])) {
            $filterKey = $this->configuration[self::CONFIG_FIELD_COLLECTION_KEY];

            $metricUnits = explode('_', $filterKey);
        }

        return (new GetProductContentPropertyValues)(
            assetResource: $element,
            hasConfig: (bool) $this->getConfiguration(self::CONFIG_FROM_LATEST_VERSION),
            isNetContent: false,
            metricUnit: (string) (fn () => end($metricUnits))()
        );
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
