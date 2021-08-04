<?php declare(strict_types=1);

namespace App\Command;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FetchDataCommand extends Command
{
    private const SOURCE = 'https://trailers.apple.com/trailers/home/rss/newtrailers.rss';
    protected static $defaultName = 'fetch:trailers';
    private const COUNT = 10; // get only 10 children

    private ClientInterface $httpClient;
    private LoggerInterface $logger;
    private EntityManagerInterface $doctrine;

    /**
     * FetchDataCommand constructor.
     *
     * @param ClientInterface        $httpClient
     * @param LoggerInterface        $logger
     * @param EntityManagerInterface $em
     * @param string|null            $name
     */
    public function __construct(ClientInterface $httpClient, LoggerInterface $logger, EntityManagerInterface $em, string $name = null)
    {
        parent::__construct($name);
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->doctrine = $em;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Fetch data from iTunes Movie Trailers')
            ->addArgument('source', InputArgument::OPTIONAL, 'Overwrite source')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info(sprintf(
            'Start %s at %s',
            __CLASS__,
            (string) date_create()->format(DATE_ATOM)
        ));
        $source = self::SOURCE;
        if ($input->getArgument('source')) {
            $source = $input->getArgument('source');
        }

        if (!is_string($source)) {
            throw new RuntimeException('Source must be string');
        }
        $io = new SymfonyStyle($input, $output);
        $io->title(sprintf('Fetch data with parameter %s', strval($source)));

        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($source, 'SimpleXMLElement', LIBXML_NOCDATA);

        if (false === $xml) {
            $io->title('XML load ERROR');
            foreach (libxml_get_errors() as $error) {
                $this->logger->critical($error->message);
            }
        }
        $this->processXml($xml);

        $this->logger->info(sprintf('End %s at %s', __CLASS__, (string) date_create()->format(DATE_ATOM)));

        return Command::SUCCESS;
    }

    protected function processXml(\SimpleXMLElement $data): void
    {
        $i = 1;
        $count = self::COUNT;
        $xml = $data->children();
        if (!property_exists($xml, 'channel')) {
            throw new RuntimeException('Could not find \'channel\' element in feed');
        }

        foreach ($xml->channel->item as $item) {
            if ($this->checkDuplicate((string) $item->title)) {
                $trailer = new Movie;
                $trailer
                    ->setTitle((string) $item->title)
                    ->setDescription((string) $item->description)
                    ->setLink((string) $item->link)
                    ->setPubDate($this->parseDate((string) $item->pubDate))
                    ->setImage($this->getImageFromXml($item))
                ;
                $this->doctrine->persist($trailer);
            }
            if ($i === $count) {
                break;
            }
            ++$i;
        }
        $this->doctrine->flush();
    }

    protected function getImageFromXml(\SimpleXMLElement $element): string | null
    {
        $str = (string) $element->children('content', true);
        $imageUrl = preg_match('@src="([^"]+)"@', $str, $match);
        $cut = ['src="', '"'];
        return str_replace($cut, '', $match[1]);
    }

    protected function parseDate(string $date): \DateTime
    {
        return new \DateTime($date);
    }

    protected function checkDuplicate(string $title): bool
    {
        $item = $this->doctrine->getRepository(Movie::class)->findOneBy(['title' => $title]);

        if ($item === null) {
            $this->logger->info('Create new Movie', ['title' => $title]);
        } else {
            $this->logger->info('Move found', ['title' => $title]);

            return false;
        }

        return true;
    }
}
