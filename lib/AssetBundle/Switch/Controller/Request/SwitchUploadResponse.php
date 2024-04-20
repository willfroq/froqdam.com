<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Controller\Request;

use Webmozart\Assert\Assert;

final class SwitchUploadResponse
{
    public function __construct(
        public readonly string $eventName,
        public readonly string $date,
        /** @var array<int, string> $actions */
        public array $actions,
        /** @var array<string, string> $statistics */
        public array $statistics,
    ) {
        Assert::string($this->eventName, 'Expected "filename" to be a string, got %s');
        Assert::string($this->date, 'Expected "date" to be a string, got %s');
        Assert::isArray($this->actions, 'Expected "actions" to be a string, got %s');
        Assert::isArray($this->statistics, 'Expected "statistics" to be a string, got %s');
    }

    /** @return  array<string, string|array<string|int, string>> */
    public function toArray(): array
    {
        return [
            'eventName' => $this->eventName,
            'date' => $this->date,
            'actions' => $this->actions,
            'statistics' => $this->statistics,
        ];
    }
}
