<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Form;

use Froq\PortalBundle\DTO\FormData\InputFilterDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FroqInputFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addModelTransformer(new CallbackTransformer(
                function ($dto) {
                    if ($dto instanceof InputFilterDto) {
                        return $dto->getText();
                    }

                    return '';
                },
                function ($text) {
                    if (!$text) {
                        return null;
                    }

                    $dto = new InputFilterDto();
                    $dto->setText($text);

                    return $dto;
                }
            ));
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
