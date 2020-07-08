<?php

namespace App\Command;

use App\Entity\Site;
use App\Repository\SiteRepository;
use App\Service\VacancyProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ParseVacancyListCommand extends Command
{
    protected static $defaultName = 'app:parse-vacancy-list';

    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var VacancyProvider $vacancyProvider */
    protected $vacancyProvider;

    public function __construct(EntityManagerInterface $entityManager, VacancyProvider $vacancyProvider)
    {
        $this->entityManager = $entityManager;
        $this->vacancyProvider = $vacancyProvider;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Parses vacancy list of specified site.')
            ->setHelp('Parses vacancy list of specified site. Pass site identifier (e.g. "dou") to make it work')
            ->addArgument('site', InputArgument::REQUIRED, 'Site identifier, one of "dou", ...')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $siteSlug = $input->getArgument('site');
        $dryRun = (bool) $input->getOption('dry-run');

        /** @var SiteRepository $siteRepository */
        $siteRepository = $this->entityManager->getRepository(Site::class);

        /** @var Site $site */
        $site = $siteRepository->findOneBy(['slug' =>$siteSlug]);

        $io->note(sprintf('Site to parse: %s', $site->getName()));

        $vacancies = $this->vacancyProvider->getVacancyListFromSite($site, !$dryRun);

        $io->success(sprintf(
            'Success! %s vacancies have been fetched from %s.',
            count($vacancies),
            $site->getName()
        ));

        return Command::SUCCESS;
    }
}
