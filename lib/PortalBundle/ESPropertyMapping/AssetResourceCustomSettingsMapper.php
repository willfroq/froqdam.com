<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ESPropertyMapping;

use Froq\PortalBundle\ESPropertyMapping\Traits\NestedFieldMapperTrait;
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

class AssetResourceCustomSettingsMapper implements
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

    private const CONFIG_NESTED_CUSTOM_SETTING = 'nested_custom_setting';
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
        $this->resolveOptions($this->configuration);

        if (!($element instanceof AssetResource)) {
            return null;
        }

        if ($this->getConfiguration(self::CONFIG_FROM_LATEST_VERSION) === true) {
            $element = AssetResourceHierarchyHelper::getLatestVersion($element);
        }

        if (!$element->getAsset()) {
            return null;
        }

        if (!$keys = explode('.', $this->getConfiguration(self::CONFIG_NESTED_CUSTOM_SETTING))) {
            return null;
        }

        $value = $element->getAsset()->getCustomSettings();
        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                $value = null;
                break;
            }
        }

        return $value;
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
            ->setRequired([self::CONFIG_NESTED_CUSTOM_SETTING])
            ->setAllowedTypes(self::CONFIG_NESTED_CUSTOM_SETTING, 'string')
            ->setAllowedValues(self::CONFIG_NESTED_CUSTOM_SETTING, [
                Validation::createIsValidCallable(new NotBlank()),
            ]);

        return $resolver->resolve($data);
    }
}
