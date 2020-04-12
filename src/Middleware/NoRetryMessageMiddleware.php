<?php

namespace IHelpShopping\Middleware;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class NoRetryMessageMiddleware implements MiddlewareInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        try {
            $envelope = $stack->next()->handle($envelope, $stack);
        } catch (\Throwable $exception) {
            $context = [
                'message' => $message,
                'class' => \get_class($envelope->getMessage()),
            ];

            $this->logger->error($exception->getMessage(), $context);

            throw new UnrecoverableMessageHandlingException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }

        return $envelope;
    }
}
