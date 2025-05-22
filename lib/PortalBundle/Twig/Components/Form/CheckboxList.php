<?php

namespace Froq\PortalBundle\Twig\Components\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'CheckboxList', template: '@FroqPortal/components/form/CheckboxList.html.twig')]
final class CheckboxList
{
    /** @var array<int, mixed> */
    public array $items = [];

    public string $class = '';

    public string $filterName = '';
}
