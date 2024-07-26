<?php

declare(strict_types=1);

namespace Froq\PortalBundle\DataTransferObject;

use Webmozart\Assert\Assert;

final class MessageItem
{
    public function __construct(
        public readonly int $messageId,
        public readonly string $queueName,
        public readonly string $messageClass,
        public readonly string $createdAt,
        public readonly string $availableAt,
    ) {
        Assert::numeric($this->messageId, 'Expected "projectItemId" to be a numeric, got %s');
        Assert::string($this->queueName, 'Expected "queueName" to be a string, got %s');
        Assert::string($this->messageClass, 'Expected "messageClass" to be a string, got %s');
        Assert::string($this->createdAt, 'Expected "createdAt" to be a string, got %s');
        Assert::string($this->availableAt, 'Expected "availableAt" to be a string, got %s');
    }
}
