<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Switch\Controller\Request;

use Webmozart\Assert\Assert;

final class UpdateResponse
{
    public function __construct(
        public readonly string $eventName,
        public readonly string $date,
        public readonly string $filename,
        public readonly int $parentAssetResourceId,
        public readonly int $latestAssetResourceId,
        public readonly int $status,
        public readonly string $message,
        /** @var array<int, string> $actions */
        public array $actions,
    ) {
        Assert::string($this->eventName, 'Expected "eventName" to be a string, got %s');
        Assert::string($this->date, 'Expected "date" to be a string, got %s');
        Assert::string($this->filename, 'Expected "filename" to be a string, got %s');
        Assert::integer($this->parentAssetResourceId, 'Expected "parentAssetResourceId" to be a int, got %s');
        Assert::integer($this->latestAssetResourceId, 'Expected "latestAssetResourceId" to be a int, got %s');
        Assert::integer($this->status, 'Expected "status" to be a int, got %s');
        Assert::string($this->message, 'Expected "message" to be a string, got %s');
        Assert::isArray($this->actions, 'Expected "actions" to be a array, got %s');
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'eventName' => $this->eventName,
            'date' => $this->date,
            'filename' => $this->filename,
            'parentAssetResourceId' => $this->parentAssetResourceId,
            'status' => $this->status,
            'latestAssetResourceId' => $this->latestAssetResourceId,
            'message' => $this->message,
            'actions' => $this->actions,
        ];
    }
}
