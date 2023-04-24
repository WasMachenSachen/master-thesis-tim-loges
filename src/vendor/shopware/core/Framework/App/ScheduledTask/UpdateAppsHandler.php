<?php declare(strict_types=1);

namespace Shopware\Core\Framework\App\ScheduledTask;

use Shopware\Core\Framework\App\Lifecycle\Update\AbstractAppUpdater;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

/**
 * @deprecated tag:v6.5.0 - reason:becomes-internal - MessageHandler will be internal and final starting with v6.5.0.0
 */
#[Package('core')]
class UpdateAppsHandler extends ScheduledTaskHandler
{
    private AbstractAppUpdater $appUpdater;

    /**
     * @internal
     */
    public function __construct(EntityRepositoryInterface $scheduledTaskRepository, AbstractAppUpdater $appUpdater)
    {
        parent::__construct($scheduledTaskRepository);
        $this->appUpdater = $appUpdater;
    }

    public function run(): void
    {
        $this->appUpdater->updateApps(Context::createDefaultContext());
    }

    public static function getHandledMessages(): iterable
    {
        return [UpdateAppsTask::class];
    }
}
