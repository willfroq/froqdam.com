<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Pimtoday\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductFromPayload
{
    public function __construct(
        #[Assert\Type(type: 'int', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly int $pimTodayId,

        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public ?int $damId,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $pimTodaySku,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid {{ type }}.')]
        public readonly ?string $pimTodayEan,
    ) {
    }
}
