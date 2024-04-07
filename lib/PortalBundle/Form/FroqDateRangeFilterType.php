<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Form;

use Froq\PortalBundle\DTO\FormData\DateRangeFilterDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FroqDateRangeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $commonOptions = [
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
            'html5' => false
        ];
        $builder
            ->add('startDate', DateType::class, array_merge($commonOptions, [
                'label' => 'From'
            ]))
            ->add('endDate', DateType::class, array_merge($commonOptions, [
                'label' => 'To'
            ]));

        $builder->addModelTransformer(new CallbackTransformer(
            function ($dto) {
                return $dto;
            },
            function ($dto) {
                if (!$dto) {
                    return null;
                }

                if (!$dto instanceof DateRangeFilterDto) {
                    return null;
                }

                if (!$dto->getStartDate() && !$dto->getEndDate()) {
                    return null;
                }

                return $dto;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DateRangeFilterDto::class
        ]);
    }
}
