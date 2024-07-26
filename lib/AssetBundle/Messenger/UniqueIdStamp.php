<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Messenger;

use Symfony\Component\Messenger\Stamp\StampInterface;

final class UniqueIdStamp implements StampInterface
{
    private string $uniqueId;

    public function __construct()
    {
        $this->uniqueId = uniqid();
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }
}
