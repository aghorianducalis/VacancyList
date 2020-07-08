<?php

namespace App\Command;

use App\Entity\Site;
use App\Repository\SiteRepository;
use App\Service\VacancyProvider;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunParsersCommand extends Command
{
    protected static $defaultName = 'app:run-parsers';

    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var LoggerInterface $logger */
    protected $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Run the parsers for all available sites')
            ->setHelp('All sites are get from database. Appropriate parser is resolved in ' . VacancyProvider::class)
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');

        /** @var SiteRepository $siteRepository */
        $siteRepository = $this->entityManager->getRepository(Site::class);

        /** @var Site[] $sites */
        $sites = $siteRepository->findAll();

        $command = $this->getApplication()->find('app:parse-vacancy-list');

        /** @var Site $site */
        foreach ($sites as $site) {
            $arguments = [
                'site' => $site->getSlug(),
                '--dry-run' => $dryRun,
            ];

            $commandInput = new ArrayInput($arguments);
            $returnCode = $command->run($commandInput, $output);

            // todo handle
            if ($returnCode !== Command::SUCCESS) {
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
