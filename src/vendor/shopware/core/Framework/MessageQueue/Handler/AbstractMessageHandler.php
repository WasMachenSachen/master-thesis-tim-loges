<?php declare(strict_types=1);

namespace Shopware\Core\Framework\MessageQueue\Handler;

use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\MessageQueue\Exception\MessageFailedException;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

/**
 * @deprecated tag:v6.5.0 - reason:reason:class-hierarchy-change - will be removed, use default symfony MessageSubscriberInterface instead
 */
#[Package('core')]
abstract class AbstractMessageHandler implements MessageSubscriberInterface
{
    /**
     * @param object $message
     */
    public function __invoke($message): void
    {
        try {
            $this->handle($message);
        } catch (MessageFailedException $messageFailedException) {
            throw $messageFailedException;
        } catch (\Throwable $e) {
            throw new MessageFailedException($message, static::class, $e);
        }
    }

    /**
     * @param object $message
     */
    abstract public function handle($message): void;

    /**
     * @return iterable<int|string>
     */
    abstract public static function getHandledMessages(): iterable;
}
