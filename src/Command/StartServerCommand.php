<?php declare(strict_types=1);

namespace App\Command;

use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command.
 */
class StartServerCommand extends Command
{
    protected static $defaultName = 'server:start';
    private ClientInterface $httpClient;
    private LoggerInterface $logger;

    /**
     * StartServerCommand constructor.
     *
     * @param ClientInterface $httpClient
     * @param LoggerInterface $logger
     * @param string|null     $name
     */
    public function __construct(ClientInterface $httpClient, LoggerInterface $logger, string $name = null)
    {
        parent::__construct($name);
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription('Start a developer PHP built-in server');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        exec('php -S localhost:8080 -t public public/index.php', $out);
        $output->writeln($out);

        return Command::SUCCESS;
    }
}
