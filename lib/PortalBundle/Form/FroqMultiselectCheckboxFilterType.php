<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Form;

use Froq\PortalBundle\DTO\AggregationChoiceDto;
use Froq\PortalBundle\DTO\FormData\FilterMetadataDto;
use Froq\PortalBundle\DTO\FormData\MultiselectCheckboxFilterDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FroqMultiselectCheckboxFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($dto) {
                if(!$dto) {
                    return null;
                }

                return $dto->getSelectedOptions();
            },
            function ($selectedOptions) {
                if (!$selectedOptions) {
                    return null;
                }

                $dto = new MultiselectCheckboxFilterDto();
                $dto->setSelectedOptions($selectedOptions);

                return $dto;
            }
        ));
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'multiple' => true,
            'expanded' => true,
        ]);
        $resolver->setRequired('filter_metadata');
        $resolver->setAllowedTypes('filter_metadata', FilterMetadataDto::class);

        $resolver->setDefault('choices', function (Options $options) {
            return $this->getChoices($options['filter_metadata']);
        });

        $resolver->setNormalizer('choice_attr', function (Options $options, $value) {
            $filterMetadata = $options['filter_metadata'];
            $docCounts = $this->getDocCounts($filterMetadata);

            return function ($choiceValue, $key, $choiceIndex) use ($docCounts) {
                return ['data-doc-count' => $docCounts[$choiceValue]];
            };
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function getChoices(FilterMetadataDto $filterMetadata): array
    {
        $choices = [];
        /** @var AggregationChoiceDto $choice */
        foreach ($filterMetadata->getAggregationChoices() as $choice) {
            $choices[sprintf('%s (%s)', $choice->getKey(), $choice->getDocCount())] = $choice->getKey();
        }

        return $choices;
    }

    /**
     * @return array<string, mixed>
     */
    private function getDocCounts(FilterMetadataDto $filterMetadata): array
    {
        $docCounts = [];
        /** @var AggregationChoiceDto $choice */
        foreach ($filterMetadata->getAggregationChoices() as $choice) {
            $docCounts[$choice->getKey()] = $choice->getDocCount();
        }

        return $docCounts;
    }
}
