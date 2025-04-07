<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ESPropertyMapping;

use Carbon\Carbon;
use Froq\PortalBundle\ESPropertyMapping\Traits\NestedFieldMapperTrait;
use Froq\PortalBundle\Exception\ES\ESPropertyMappingException;
use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
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

class NestedFieldMapper extends AbstractMapper implements
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

            $values = $this->getNestedFieldValues(
                $element,
                $this->propertyName,
                explode('.', $this->getConfiguration(self::CONFIG_NESTED_FIELD))
            );

            foreach ($values ?? [] as &$value) {
                if ($value instanceof Carbon) {
                    $value = $value->timestamp;
                }
            }

            if ($values && count($values) === 1) {
                return $values[0] instanceof Carbon ? $values[0]->timestamp : $values[0];
            }

            return $values;
        } catch (\Exception $exception) {
            $this->logger->error(sprintf(
                '%s: %s',
                ESPropertyMappingException::PROPERTY_MAPPING_EXCEPTION,
                $exception->getMessage()
            ));
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
            ->setRequired([self::CONFIG_NESTED_FIELD])
            ->setAllowedTypes(self::CONFIG_NESTED_FIELD, 'string')
            ->setAllowedValues(self::CONFIG_NESTED_FIELD, [
                Validation::createIsValidCallable(new NotBlank()),
            ]);

        return $resolver->resolve($data);
    }
}
