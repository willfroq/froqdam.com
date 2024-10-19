<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Twig\Components;

use Pimcore\Model\DataObject\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'FieldValue', template: '@FroqPortal/components/FieldValue.html.twig')]
final class FieldValue extends AbstractController
{
    public User $user;
    public ?string $fieldValue;
    public ?string $textFieldName;
    public ?string $keywordFieldName;
    public ?string $keywordFieldUrl;
    public ?string $textFieldUrl;
}
