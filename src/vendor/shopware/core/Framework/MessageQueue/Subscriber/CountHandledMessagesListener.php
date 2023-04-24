<?php declare(strict_types=1);

namespace Shopware\Core\Framework\MessageQueue\Subscriber;

use Shopware\Core\Framework\Log\Package;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Symfony\Component\Messenger\EventListener\StopWorkerOnTimeLimitListener;

/**
 * @deprecated tag:v6.5.0 - reason:becomes-internal - EventSubscribers will become internal in v6.5.0
 */
#[Package('system-settings')]
class CountHandledMessagesListener extends StopWorkerOnTimeLimitListener
{
    private int $handledMessages = 0;

    public function onWorkerRunning(WorkerRunningEvent $event): void
    {
        if (!$event->isWorkerIdle()) {
            ++$this->handledMessages;
        }

        parent::onWorkerRunning($event);
    }

    public function getHandledMessages(): int
    {
        return $this->handledMessages;
    }
}
