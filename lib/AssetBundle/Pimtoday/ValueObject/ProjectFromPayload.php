<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ProjectFromPayload
{
    public function __construct(
        #[NotBlank(message: 'pimTodayId can not be blank.')]
        public readonly int $pimTodayId,

        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public ?int $damId,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $projectNumber,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public string $froqProjectNumber,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $projectName,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $description,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $projectType,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $status,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $location,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $projectOwner,
    ) {
    }
}
