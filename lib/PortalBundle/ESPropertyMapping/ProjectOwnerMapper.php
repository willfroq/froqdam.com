<?php

declare(strict_types=1);

namespace Froq\PortalBundle\ESPropertyMapping;

use Froq\PortalBundle\ESPropertyMapping\Traits\NestedFieldMapperTrait;
use Pimcore\Model\DataObject\AssetResource;
use Pimcore\Model\DataObject\ProjectRole;
use Pimcore\Model\DataObject\User;
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

class ProjectOwnerMapper implements
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

    private const CONFIG_NESTED_CONTACTS_FIELD = 'nested_contacts';
    private const CONFIG_PROJECT_ROLE_CODE = 'project_role_code';

    /**
     * @return array<string|int, mixed>
     */
    public function getDefinition(): array
    {
        return $this->getConfiguredDefinition() ?: [
            'type' => 'nested',
            'properties' => [
                'user_id' => [
                    'type' => 'integer'
                ],
                'user_name' => [
                    'type' => 'keyword'
                ]
            ]
        ];
    }

    /**
     * @param object $element
     *
     * @return bool|int|float|string|array<string|int, mixed>|null
     */
    public function translate(object $element): bool|int|float|string|array|null
    {
        $this->resolveOptions($this->configuration);

        if (!$element instanceof AssetResource) {
            return null;
        }

        $contacts = $this->getNestedFieldValues(
            $element,
            $this->propertyName,
            explode('.', $this->getConfiguration(self::CONFIG_NESTED_CONTACTS_FIELD))
        );

        $values = [];
        foreach ($contacts ?? [] as $contact) {
            if (!$contact || !isset($contact['Person']) || !isset($contact['Role'])) {
                continue;
            }

            $user = $contact['Person']->getData();
            $role = $contact['Role']->getData();

            if (!($user instanceof User) || !($role instanceof ProjectRole)) {
                continue;
            }

            if ($role->getCode() === $this->getConfiguration(self::CONFIG_PROJECT_ROLE_CODE)) {
                $ids = array_column($values, 'user_id');
                if ($user->getId() && $user->getName() && !isset($ids[$user->getId()])) {
                    $values[] = [
                        'user_id' => $user->getId(),
                        'user_name' => $user->getName()
                    ];
                }
            }
        }

        return $values;
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
            ->setRequired([self::CONFIG_NESTED_CONTACTS_FIELD, self::CONFIG_PROJECT_ROLE_CODE])
            ->setAllowedTypes(self::CONFIG_NESTED_CONTACTS_FIELD, 'string')
            ->setAllowedValues(self::CONFIG_NESTED_CONTACTS_FIELD, [
                Validation::createIsValidCallable(new NotBlank()),
            ])
            ->setAllowedTypes(self::CONFIG_PROJECT_ROLE_CODE, 'string')
            ->setAllowedValues(self::CONFIG_PROJECT_ROLE_CODE, [
                Validation::createIsValidCallable(new NotBlank()),
            ]);

        return $resolver->resolve($data);
    }
}
