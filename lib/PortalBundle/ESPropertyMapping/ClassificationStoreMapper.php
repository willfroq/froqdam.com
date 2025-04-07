<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ESPropertyMapping;

use Froq\PortalBundle\ESPropertyMapping\Traits\NestedFieldMapperTrait;
use Froq\PortalBundle\Exception\ES\ESPropertyMappingException;
use Froq\PortalBundle\Helper\AssetResourceHierarchyHelper;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\Classificationstore;
use Pimcore\Model\DataObject\Classificationstore\Group;
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

class ClassificationStoreMapper extends AbstractMapper implements
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

    private const CONFIG_NESTED_CS_FIELD = 'nested_cs_field';
    private const CONFIG_CS_GROUP_NAME = 'cs_group_name';
    private const CONFIG_CS_KEY_NAME = 'cs_key_name';
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

            $classificationStores = $this->getNestedFieldValues(
                $element,
                $this->propertyName,
                explode('.', $this->getConfiguration(self::CONFIG_NESTED_CS_FIELD))
            );

            $values = [];
            foreach ($classificationStores ?? [] as $cs) {
                if ($value = $this->getValueByConfiguredCSKey($cs)) {
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

    /**
     * @param Classificationstore $cs
     *
     * @return string|null
     *
     * @throws \Exception
     */
    private function getValueByConfiguredCSKey(Classificationstore $cs): ?string
    {
        $group = $this->getCsGroupByConfiguredName($cs);
        if (!$group) {
            return null;
        }

        foreach ($group->getKeys() as $key) {
            if (strtolower($key->getConfiguration()->getName()) === strtolower($this->getConfiguration(self::CONFIG_CS_KEY_NAME))) {
                return (string)$cs->getLocalizedKeyValue((int)$group->getConfiguration()->getId(), (int)$key->getConfiguration()->getId(), 'en');
            }
        }

        return null;
    }

    /**
     * @param Classificationstore $cs
     *
     * @return Group|null
     */
    private function getCsGroupByConfiguredName(Classificationstore $cs): ?Group
    {
        foreach ($cs->getGroups() as $group) {
            if (strtolower($group->getConfiguration()->getName()) == strtolower($this->getConfiguration(self::CONFIG_CS_GROUP_NAME))) {
                return $group;
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
            ->setRequired([self::CONFIG_NESTED_CS_FIELD, self::CONFIG_CS_GROUP_NAME, self::CONFIG_CS_KEY_NAME])
            ->setAllowedTypes(self::CONFIG_NESTED_CS_FIELD, 'string')
            ->setAllowedValues(self::CONFIG_NESTED_CS_FIELD, [
                Validation::createIsValidCallable(new NotBlank()),
            ])
            ->setAllowedTypes(self::CONFIG_CS_GROUP_NAME, 'string')
            ->setAllowedValues(self::CONFIG_CS_GROUP_NAME, [
                Validation::createIsValidCallable(new NotBlank()),
            ])
            ->setAllowedTypes(self::CONFIG_CS_KEY_NAME, 'string')
            ->setAllowedValues(self::CONFIG_CS_KEY_NAME, [
                Validation::createIsValidCallable(new NotBlank()),
            ]);

        return $resolver->resolve($data);
    }
}
