<?php

declare(strict_types=1);

namespace Froq\AssetBundle\Messenger;

use Froq\AssetBundle\Action\Messenger\GetMessageContextForLog;
use Pimcore\Log\ApplicationLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Stamp\SentStamp;
use Symfony\Component\Messenger\Stamp\SentToFailureTransportStamp;

final class AuditMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly LoggerInterface $messengerAuditLogger,
        private readonly ApplicationLogger $logger,
        private readonly GetMessageContextForLog $getMessageContextForLog,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (null === $envelope->last(stampFqcn: UniqueIdStamp::class)) {
            $envelope = $envelope->with(stamps: new UniqueIdStamp());
        }

        /** @var UniqueIdStamp $stamp */
        $stamp = $envelope->last(stampFqcn: UniqueIdStamp::class);

        $messageObject = $envelope->getMessage();

        $message = ($this->getMessageContextForLog)($messageObject);

        $context = [
            'id' => $stamp->getUniqueId(),
            'class' => get_class($messageObject),
            'message' => $message,
            'fileObject'=> get_class($messageObject),
            'component' => $message['queueName'] ?? 'from_messenger'
        ];

        $envelope = $stack->next()->handle(envelope: $envelope, stack: $stack);

        if ($envelope->last(stampFqcn: ReceivedStamp::class)) {
            $this->messengerAuditLogger->info(message: '[{id} Received {class}]', context: $context);

            $this->logger->info(message: (string) json_encode(array_merge($context, ['status' => 'ReceivedStamp'])), context: $context);
        }

        if ($envelope->last(stampFqcn: SentStamp::class)) {
            $this->messengerAuditLogger->info(message: '[{id} Sent {class}]', context: $context);

            $this->logger->info(message: (string) json_encode(array_merge($context, ['status' => 'SentStamp'])), context: $context);
        }

        if (!$envelope->last(stampFqcn: ReceivedStamp::class)) {
            $this->messengerAuditLogger->info(message: '[{id} Handling sync {class}]', context: $context);

            $this->logger->info(message: (string) json_encode(array_merge($context, ['status' => 'ReceivedStamp sync'])), context: $context);
        }

        if ($envelope->last(stampFqcn: ErrorDetailsStamp::class)) {
            $this->messengerAuditLogger->info(message: '[{id} ErrorDetailsStamp {class}]', context: $context);

            $this->logger->info(message: (string) json_encode(array_merge($context, ['status' => 'ErrorDetailsStamp'])), context: $context);
        }

        if ($envelope->last(stampFqcn: SentToFailureTransportStamp::class)) {
            $this->messengerAuditLogger->info(message: '[{id} SentToFailureTransportStamp {class}]', context: $context);

            $this->logger->info(message: (string) json_encode(array_merge($context, ['status' => 'SentToFailureTransportStamp'])), context: $context);
        }

        return $envelope;
    }
}
