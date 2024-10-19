<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;
use Webmozart\Assert\Assert as AssertProps;

final class ProjectFromPayload
{
    public function __construct(
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $projectNumber,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $projectName,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $description,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $projectType,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $status,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $location,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly string $projectOwner,
    ) {
        AssertProps::string($this->projectNumber, 'Expected "projectNumber" to be a string, got %s');
        AssertProps::string($this->projectName, 'Expected "projectName" to be a string, got %s');
        AssertProps::string($this->description, 'Expected "description" to be a string, got %s');
        AssertProps::string($this->projectType, 'Expected "projectType" to be a string, got %s');
        AssertProps::string($this->status, 'Expected "status" to be a string, got %s');
        AssertProps::string($this->location, 'Expected "location" to be a string, got %s');
        AssertProps::string($this->projectOwner, 'Expected "projectOwner" to be a string, got %s');
    }
}
