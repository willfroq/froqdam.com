<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ESPropertyMapping;

use Froq\PortalBundle\ESPropertyMapping\Traits\NestedFieldMapperTrait;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Category;
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

class CategoryLevelLabelMapper implements
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

    private const CONFIG_NESTED_CATEGORIES_FIELD = 'nested_categories';
    private const CONFIG_LEVEL_LABEL = 'level_label';

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

        if (!$element instanceof AbstractObject) {
            return null;
        }

        $categories = $this->getNestedFieldValues(
            $element,
            $this->propertyName,
            explode('.', $this->getConfiguration(self::CONFIG_NESTED_CATEGORIES_FIELD))
        );

        $values = [];
        foreach ($categories ?? [] as $category) {
            if ($category && ($value = $this->getKeyByConfiguredTypes($category))) {
                $values[] = $value;
            }
        }

        return array_unique($values);
    }

    /**
     * @param Category $category
     *
     * @return string|null
     */
    private function getKeyByConfiguredTypes(Category $category): ?string
    {
        $configuredLevelLabels = array_map('strtolower', $this->getConfiguration(self::CONFIG_LEVEL_LABEL));

        if (in_array(strtolower((string) $category->getLevelLabel()), $configuredLevelLabels)) {
            return $category->getKey();
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
            ->setRequired([self::CONFIG_NESTED_CATEGORIES_FIELD, self::CONFIG_LEVEL_LABEL])
            ->setAllowedTypes(self::CONFIG_NESTED_CATEGORIES_FIELD, 'string')
            ->setAllowedValues(self::CONFIG_NESTED_CATEGORIES_FIELD, [
                Validation::createIsValidCallable(new NotBlank()),
            ])
            ->setAllowedTypes(self::CONFIG_LEVEL_LABEL, 'array')
            ->setAllowedValues(self::CONFIG_NESTED_CATEGORIES_FIELD, [
                Validation::createIsValidCallable(new NotBlank()),
            ]);

        return $resolver->resolve($data);
    }
}
