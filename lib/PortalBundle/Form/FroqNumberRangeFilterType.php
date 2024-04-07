<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Form;

use Froq\PortalBundle\DTO\FormData\NumberRangeFilterDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FroqNumberRangeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('min', IntegerType::class, [
                'label' => 'Minimum'
            ])
            ->add('max', IntegerType::class, [
                'label' => 'Maximum'
            ]);

        $builder->addModelTransformer(new CallbackTransformer(
            function ($dto) {
                return $dto;
            },
            function ($dto) {
                if (!$dto) {
                    return null;
                }

                if (!$dto instanceof NumberRangeFilterDto) {
                    return null;
                }

                if (!$dto->getMax() && !$dto->getMin()) {
                    return null;
                }

                return $dto;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NumberRangeFilterDto::class,
        ]);
    }
}
