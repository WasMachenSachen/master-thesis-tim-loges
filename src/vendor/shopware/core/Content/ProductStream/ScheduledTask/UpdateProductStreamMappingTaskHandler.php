<?php declare(strict_types=1);

namespace Shopware\Core\Content\ProductStream\ScheduledTask;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

/**
 * @deprecated tag:v6.5.0 - reason:becomes-internal - MessageHandler will be internal and final starting with v6.5.0.0
 */
#[Package('business-ops')]
class UpdateProductStreamMappingTaskHandler extends ScheduledTaskHandler
{
    private EntityRepositoryInterface $productStreamRepository;

    /**
     * @internal
     */
    public function __construct(
        EntityRepositoryInterface $repository,
        EntityRepositoryInterface $productStreamRepository
    ) {
        parent::__construct($repository);
        $this->productStreamRepository = $productStreamRepository;
    }

    public static function getHandledMessages(): iterable
    {
        return [UpdateProductStreamMappingTask::class];
    }

    public function run(): void
    {
        $context = Context::createDefaultContext();
        $criteria = new Criteria();
        $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
            new EqualsFilter('filters.type', 'until'),
            new EqualsFilter('filters.type', 'since'),
        ]));

        /** @var array<string> $streamIds */
        $streamIds = $this->productStreamRepository->searchIds($criteria, $context)->getIds();
        $data = array_map(function (string $id) {
            return ['id' => $id];
        }, $streamIds);

        $this->productStreamRepository->update($data, $context);
    }
}
