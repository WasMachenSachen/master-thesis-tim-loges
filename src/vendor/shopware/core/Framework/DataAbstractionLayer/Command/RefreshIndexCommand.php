<?php declare(strict_types=1);

namespace Shopware\Core\Framework\DataAbstractionLayer\Command;

use Shopware\Core\Framework\Adapter\Console\ShopwareStyle;
use Shopware\Core\Framework\DataAbstractionLayer\Event\RefreshIndexEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Shopware\Core\Framework\Log\Package;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @deprecated tag:v6.5.0 - reason:becomes-internal - EventSubscribers will become internal in v6.5.0
 */
#[Package('core')]
class RefreshIndexCommand extends Command implements EventSubscriberInterface
{
    use ConsoleProgressTrait;

    protected static $defaultName = 'dal:refresh:index';

    private EntityIndexerRegistry $registry;

    private EventDispatcherInterface $eventDispatcher;

    /**
     * @internal
     */
    public function __construct(EntityIndexerRegistry $registry, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct();
        $this->registry = $registry;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Refreshes the shop indices')
            ->addOption('use-queue', null, InputOption::VALUE_NONE, 'Ignore cache and force generation')
            ->addOption('skip', null, InputArgument::OPTIONAL, 'Comma separated list of indexer names to be skipped')
            ->addOption('only', null, InputArgument::OPTIONAL, 'Comma separated list of indexer names to be generated')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new ShopwareStyle($input, $output);

        $skip = \is_string($input->getOption('skip')) ? explode(',', $input->getOption('skip')) : [];
        $only = \is_string($input->getOption('only')) ? explode(',', $input->getOption('only')) : [];

        $this->registry->index($input->getOption('use-queue'), $skip, $only);

        $skipEntities = array_map(function ($indexer) {
            return str_replace('.indexer', '', $indexer);
        }, $skip);

        $onlyEntities = array_map(function ($indexer) {
            return str_replace('.indexer', '', $indexer);
        }, $only);

        $event = new RefreshIndexEvent(!$input->getOption('use-queue'), $skipEntities, $onlyEntities);
        $this->eventDispatcher->dispatch($event);

        return self::SUCCESS;
    }
}
