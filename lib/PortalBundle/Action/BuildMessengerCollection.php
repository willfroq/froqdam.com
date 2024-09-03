<?php

declare(strict_types=1);

namespace Froq\PortalBundle\Action;

use Doctrine\DBAL\Driver\Exception;
use Froq\AssetBundle\Action\Messenger\GetMessageClass;
use Froq\PortalBundle\DataTransferObject\MessageCollection;
use Froq\PortalBundle\DataTransferObject\MessageItem;
use Froq\PortalBundle\Repository\MessengerRepository;

final class BuildMessengerCollection
{
    public function __construct(private readonly MessengerRepository $messengerRepository, private readonly GetMessageClass $getMessageClass)
    {
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $currentPage, string $queueName): MessageCollection
    {
        $limit = 12;

        $messages = $this->messengerRepository->getMessages($currentPage, $limit, $queueName);
        $totalMessages = $this->messengerRepository->countAll();
        $totalPages = ceil($totalMessages / $limit);

        $messageItems = [];

        foreach ($messages as $messageId => $message) {
            $body = $message['body'] ?? '';

            $messageItems[] = new MessageItem(
                messageId: (int) $messageId,
                queueName: $message['queue_name'] ?? '',
                messageClass: ($this->getMessageClass)($body),
                createdAt: $message['created_at'] ?? '',
                availableAt: $message['available_at'] ?? '',
            );
        }

        return new MessageCollection(
            totalPages: (int) $totalPages,
            currentPage: $currentPage,
            currentQueueName: $queueName,
            messageItems: $messageItems,
        );
    }
}
