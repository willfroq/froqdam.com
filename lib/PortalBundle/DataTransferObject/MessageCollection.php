<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DataTransferObject;

use Webmozart\Assert\Assert;

final class MessageCollection
{
    public function __construct(
        public readonly int $totalPages,
        public readonly int $currentPage,
        public readonly string $currentQueueName,
        /** @var array<int, MessageItem> */
        public readonly array $messageItems,
    ) {
        Assert::numeric($this->totalPages, 'Expected "totalPages" to be a numeric, got %s');
        Assert::numeric($this->currentPage, 'Expected "currentPage" to be a numeric, got %s');
        Assert::string($this->currentQueueName, 'Expected "currentQueueName" to be a string, got %s');
        Assert::isArray($this->messageItems, 'Expected "messageItems" to be an array, got %s');
    }

    /** @return array<string, int|string|array<int, MessageItem>> */
    public function toArray(): array
    {
        return [
            'totalPages' => $this->totalPages,
            'currentPage' => $this->currentPage,
            'currentQueueName' => $this->currentQueueName,
            'messageItems' => $this->messageItems,
        ];
    }
}
