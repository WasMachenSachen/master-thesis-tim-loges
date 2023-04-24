<?php declare(strict_types=1);

namespace Shopware\Core\Framework\MessageQueue\Exception;

use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\ShopwareHttpException;

/**
 * @deprecated tag:v6.5.0 - reason:remove-decorator - will be removed, as we use default symfony retry mechanism
 */
#[Package('core')]
class MessageFailedException extends ShopwareHttpException
{
    /**
     * @var object
     */
    private $originalMessage;

    /**
     * @var string
     */
    private $handlerClass;

    /**
     * @var \Throwable
     */
    private $exception;

    public function __construct(object $originalMessage, string $handlerClass, \Throwable $exception)
    {
        $this->originalMessage = $originalMessage;
        $this->handlerClass = $handlerClass;
        $this->exception = $exception;

        parent::__construct(
            'The handler "{{ handlerClass }}" threw a "{{ exceptionClass }}" for message "{{ messageClass }}". The Exception was "{{ exceptionMessage }}.',
            [
                'handlerClass' => $handlerClass,
                'exceptionClass' => \get_class($exception),
                'messageClass' => \get_class($originalMessage),
                'exceptionMessage' => $exception->getMessage(),
            ]
        );
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }

    public function getOriginalMessage(): object
    {
        return $this->originalMessage;
    }

    public function getHandlerClass(): string
    {
        return $this->handlerClass;
    }

    public function getErrorCode(): string
    {
        return 'FRAMEWORK__QUEUE_MESSAGE_FAILED';
    }
}
