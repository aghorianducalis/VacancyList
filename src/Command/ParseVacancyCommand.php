<?php

namespace App\Command;

use App\Entity\Vacancy;
use App\Service\VacancyProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ParseVacancyCommand extends Command
{
    protected static $defaultName = 'app:parse-vacancy';

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
        $this->setDescription('Parses the single specified vacancy.')
            ->setHelp('Parses the single specified vacancy (linked to site). Pass the vacancy ID (integer) to make it work')
            ->addArgument('vacancy', InputArgument::REQUIRED, 'Vacancy identifier, must be an integer value')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run identifier');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $vacancyId = (int) $input->getArgument('vacancy');
        $dryRun = (bool) $input->getOption('dry-run');

        /** @var Vacancy|null $vacancy */
        $vacancy = $this->entityManager->getRepository(Vacancy::class)->findOneByIdJoinedToSite($vacancyId);

        if (!$vacancy) {
            $io->error(sprintf('No vacancy found for ID ' . $vacancyId));

            return Command::FAILURE;
        }

        $io->note(sprintf('Parse the vacancy at the link: %s', $vacancy->getUrl()));

        $vacancy = $this->vacancyProvider->getVacancyFromSite($vacancy, !$dryRun);

        $io->success('Success! Vacancy has been parsed.');

        return Command::SUCCESS;
    }
}
