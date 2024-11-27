<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Controller\Request;

use Froq\AssetBundle\Switch\Validator\OrganizationExists;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Webmozart\Assert\Assert as AssertProps;

final class CleanupAssetsRequest
{
    public function __construct(
        #[NotBlank(message: 'EventName can not be blank.')]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $eventName,
        #[NotBlank(message: 'CustomerCode can not be blank.')]
        #[OrganizationExists]
        public readonly string $customerCode
    ) {
        AssertProps::string($this->eventName, 'Expected "eventName" to be a string, got %s');
        AssertProps::string($this->customerCode, 'Expected "customerCode" to be a string, got %s');
    }
}
