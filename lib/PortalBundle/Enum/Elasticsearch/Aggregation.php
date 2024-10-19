<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Enum\Elasticsearch;

enum Aggregation: int
{
    case SizeLimit = 1;

    public function readable(): int
    {
        return match ($this) {
            self::SizeLimit => 200,
        };
    }
}
