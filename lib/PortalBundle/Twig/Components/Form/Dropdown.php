<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Dropdown', template: '@FroqPortal/components/form/Dropdown.html.twig')]
final class Dropdown
{
    public string $id = '';
    public string $label = '';
    /** @var array<string, string> */
    public array $options = [];
    public string $selected = '';
    public ?string $width = null;
    public bool $autocomplete = true;
    public string $placeholder = 'Search...';
    public string $searchUrl = '';
}
