<?php declare(strict_types=1);

namespace AiDescription\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AiDescription\Service\CallApi;

class ExampleCommand extends Command
{
    private $callApi;
    public function __construct(
        CallApi $callApi,
        string $name = null
    ) {
        parent::__construct();
        $this->callApi = $callApi;
    }

    // Provides a description, printed out in bin/console
    protected function configure(): void
    {
        $this->setName('ai-description:example')->setDescription('Does something very special.');
    }

    // Actual code executed in the command
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Call the service

        $output->writeln($this->callApi->callApi());

        // Exit code 0 for success
        return 0;
    }
}
