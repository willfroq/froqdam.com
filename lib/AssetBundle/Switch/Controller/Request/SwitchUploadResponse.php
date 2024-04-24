<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Controller\Request;

use Webmozart\Assert\Assert;

final class SwitchUploadResponse
{
    public function __construct(
        public readonly string $eventName,
        public readonly string $date,
        public readonly string $logLevel,
        public readonly string $assetId,
        public readonly string $assetResourceId,
        /** @var array<string, int|string|null> $relatedObjects */
        public array $relatedObjects,
        /** @var array<int, string> $actions */
        public array $actions,
        /** @var array<string, string> $statistics */
        public array $statistics,
    ) {
        Assert::string($this->eventName, 'Expected "filename" to be a string, got %s');
        Assert::string($this->date, 'Expected "date" to be a string, got %s');
        Assert::string($this->logLevel, 'Expected "logLevel" to be a string, got %s');
        Assert::string($this->assetId, 'Expected "assetId" to be a string, got %s');
        Assert::string($this->assetResourceId, 'Expected "assetResourceId" to be a string, got %s');
        Assert::isArray($this->relatedObjects, 'Expected "relatedObjects" to be a array, got %s');
        Assert::isArray($this->actions, 'Expected "actions" to be a array, got %s');
        Assert::isArray($this->statistics, 'Expected "statistics" to be a array, got %s');
    }

    /** @return array<string, array<int|string, int|string|null>|string> */
    public function toArray(): array
    {
        return [
            'eventName' => $this->eventName,
            'date' => $this->date,
            'logLevel' => $this->logLevel,
            'assetId' => $this->assetId,
            'assetResourceId' => $this->assetResourceId,
            'relatedObjects' => $this->relatedObjects,
            'actions' => $this->actions,
            'statistics' => $this->statistics,
        ];
    }
}
