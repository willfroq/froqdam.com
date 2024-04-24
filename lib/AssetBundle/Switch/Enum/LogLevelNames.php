<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Enum;

enum LogLevelNames: int
{
    case SUCCESS = 1;
    case WARNING = 2;
    case ERROR = 3;

    public function readable(): string
    {
        return match ($this) {
            self::SUCCESS => 'SUCCESS',
            self::WARNING => 'WARNING',
            self::ERROR => 'ERROR',
        };
    }
}
