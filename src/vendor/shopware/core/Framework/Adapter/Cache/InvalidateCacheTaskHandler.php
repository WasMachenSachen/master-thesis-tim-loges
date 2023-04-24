<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Adapter\Cache;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

/**
 * @internal
 */
#[Package('core')]
final class InvalidateCacheTaskHandler extends ScheduledTaskHandler
{
    private CacheInvalidator $cacheInvalidator;

    private int $delay;

    public function __construct(EntityRepositoryInterface $scheduledTaskRepository, CacheInvalidator $cacheInvalidator, int $delay)
    {
        parent::__construct($scheduledTaskRepository);

        $this->cacheInvalidator = $cacheInvalidator;
        $this->delay = $delay;
    }

    public static function getHandledMessages(): iterable
    {
        return [InvalidateCacheTask::class];
    }

    public function run(): void
    {
        try {
            if ($this->delay <= 0) {
                $this->cacheInvalidator->invalidateExpired(null);

                return;
            }

            $time = new \DateTime();
            $time->modify(sprintf('-%s second', $this->delay));
            $this->cacheInvalidator->invalidateExpired($time);
        } catch (\Throwable $e) {
        }
    }
}
