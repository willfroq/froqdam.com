<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Form;

use Froq\PortalBundle\DTO\FormData\LibraryFormDto;
use Froq\PortalBundle\Manager\ES\AssetLibrary\AssetLibSortManager;
use Pimcore\Model\DataObject\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssetLibSearchFormType extends AbstractType
{
    public const DEFAULT_PAGE_SIZE = 24;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Todo: Add some constraints
        $builder
            ->add('filters', FilterCollectionType::class, [
                'required' => false,
                'user' => $options['user'],
                'filters_metadata' => $options['filters_metadata'],
            ])
            ->add('query', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Search by asset (file)name, project, product or other details'
                ]
            ])
            ->add('page', IntegerType::class, [
                'label' => false,
                'required' => false,
                'data'=> 1,
                'attr' => [
                    'style' => 'display:none;',
                ],
            ])
            ->add('size', IntegerType::class, [
                'label' => false,
                'required' => false,
                'data'=> self::DEFAULT_PAGE_SIZE,
                'attr' => [
                    'style' => 'display:none;',
                ],
            ])
            ->add('sort_by', ChoiceType::class, [
                'label' => false,
                'choices' => $options['sort_choices'],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'placeholder' => false,
                'empty_data' => AssetLibSortManager::DEFAULT_SORT_BY,
                'attr' => ['user' => $options['user']]
            ])
            ->add('sort_direction', ChoiceType::class, [
                'label' => false,
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'placeholder' => false,
                'empty_data' => AssetLibSortManager::DEFAULT_SORT_DIRECTION,
                'choices' => [
                    'asc' => 'asc',
                    'desc' => 'desc'
                ],
                'attr' => [
                    'style' => 'display:none;',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => LibraryFormDto::class,
                'csrf_protection' => false,
                'allow_extra_fields' => true,
                'user' => null,
                'sort_choices' => [],
                'filters_metadata' => []
            ])->setAllowedTypes('user', User::class)
            ->setAllowedTypes('filters_metadata', 'array')
            ->setAllowedTypes('sort_choices', 'array');
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
