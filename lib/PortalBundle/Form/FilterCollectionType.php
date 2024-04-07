<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Form;

use Froq\PortalBundle\DTO\FormData\FilterMetadataDto;
use Froq\PortalBundle\ESPropertyMapping\MappingTypes;
use Froq\PortalBundle\Manager\UserSettings\AssetLibrary\FilterConfigurationManager;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterCollectionType extends AbstractType
{
    public function __construct(private readonly FilterConfigurationManager $filterConfigManager)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $filtersMetadata = $options['filters_metadata'];

        /** @var FilterMetadataDto $dto */
        foreach ($filtersMetadata as $dto) {
            $formType = $this->getFormTypeForFilterType($dto->getType());
            $commonOptions = [
                'label' => $this->filterConfigManager->getAvailableFilterLabel($dto->getFieldName(), $options['user']),
            ];
            if ($formType === FroqMultiselectCheckboxFilterType::class) {
                $commonOptions = array_merge($commonOptions, ['filter_metadata' => $dto]);
            }

            $builder->add($dto->getFieldName(), $formType, $commonOptions);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'csrf_protection' => false,
                'user' => null,
                'sort_choices' => [],
                'filters_metadata' => []
            ])->setAllowedTypes('user', User::class)
            ->setAllowedTypes('filters_metadata', 'array')
            ->setAllowedTypes('sort_choices', 'array');
    }

    private function getFormTypeForFilterType(string $filterType): string
    {
        return match ($filterType) {
            MappingTypes::MAPPING_TYPE_INTEGER => FroqNumberRangeFilterType::class,
            MappingTypes::MAPPING_TYPE_DATE => FroqDateRangeFilterType::class,
            MappingTypes::MAPPING_TYPE_KEYWORD => FroqMultiselectCheckboxFilterType::class,
            MappingTypes::MAPPING_TYPE_TEXT => FroqInputFilterType::class,
            default => throw new \InvalidArgumentException("Invalid filter type: $filterType"),
        };
    }
}
