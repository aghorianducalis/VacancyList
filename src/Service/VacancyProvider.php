<?php

namespace App\Service;

use App\Entity\Parser;
use App\Entity\Site;
use App\Entity\Vacancy;
use App\Repository\VacancyRepository;
use Doctrine\ORM\EntityManagerInterface;

class VacancyProvider
{
    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var VacancyRepository $vacancyRepository */
    protected $vacancyRepository;

    public function __construct(EntityManagerInterface $entityManager, VacancyRepository $vacancyRepository) {
        $this->entityManager = $entityManager;
        $this->vacancyRepository = $vacancyRepository;
    }

    /**
     * @param Site $site
     * @param bool $persist
     * @return Vacancy[]
     */
    public function getVacancyListFromSite(Site $site, bool $persist = false) : array
    {
        $vacancies = [];

        /** @var Parser $ormParser */
        $ormParser = $site->getParser();

        // todo make singleton, resolve via DI
        $class = $ormParser->getClass();

        /** @var ParserInterface $parser */
        $parser = new $class();

        $url = $parser->getVacancyListUrl();
        $vacancyLinks = $parser->parseVacancyList($url);

        if ($persist !== false) {
            foreach ($vacancyLinks as $link) {
                $vacancies[] = $this->vacancyRepository->createOrUpdateByUrl($link, $site);
            }
        }

        return $vacancies;
    }

    /**
     * @param Vacancy $vacancy
     * @param bool $persist
     * @return Vacancy
     */
    public function getVacancyFromSite(Vacancy $vacancy, bool $persist = false) : Vacancy
    {
        // todo make singleton, resolve via DI
        $class = $vacancy->getSite()->getParser()->getClass();

        /** @var ParserInterface $parser */
        $parser = new $class();

        $url = $vacancy->getUrl();
        $data = $parser->parseVacancy($url);

        if ($persist !== false) {
            $vacancy->setTitle($data['title']);
            $vacancy->setDescription($data['description']);

            $this->entityManager->flush();
        }

        return $vacancy;
    }
}