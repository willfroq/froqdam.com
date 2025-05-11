<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Opensearch\Enum;

enum FilterTypes: int
{
    case Input = 1;
    case Keyword = 2;
    case Date = 3;
    case Integer = 4;
    case Text = 5;

    public function readable(): string
    {
        return match ($this) {
            self::Input => 'input',
            self::Keyword => 'keyword',
            self::Date => 'date',
            self::Integer => 'integer',
            self::Text => 'text',
        };
    }
}
