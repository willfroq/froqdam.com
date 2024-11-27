<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Controller\Request;

use Webmozart\Assert\Assert;

final class CleanupAssetsResponse
{
    public function __construct(
        public readonly string $eventName,
        /** @var array<int, string> $actions */
        public array $actions,
    ) {
        Assert::string($this->eventName, 'Expected "filename" to be a string, got %s');
        Assert::isArray($this->actions, 'Expected "actions" to be a array, got %s');
    }

    /** @return array<string, array<int|string, int|string|null>|string> */
    public function toArray(): array
    {
        return [
            'eventName' => $this->eventName,
            'actions' => $this->actions,
        ];
    }
}
