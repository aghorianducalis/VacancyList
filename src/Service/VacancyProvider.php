<?php


namespace App\Service;


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

    /** @var DouParser $parser */
    protected $parser;

    public function __construct(
        EntityManagerInterface $entityManager,
        VacancyRepository $vacancyRepository,
        DouParser $parser
    ) {
        $this->entityManager = $entityManager;
        $this->vacancyRepository = $vacancyRepository;
        $this->parser = $parser;
    }

    /**
     * @param Site $site
     * @param bool $persist
     * @return Vacancy[]
     */
    public function getVacancyListFromSite(Site $site, bool $persist = false) : array
    {
        $vacancies = [];
        $url = $site->getItemListUrl();
        $vacancyLinks = $this->parser->parseItemList($url);

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
        $url = $vacancy->getUrl();
        $data = $this->parser->parseItem($url);

        if ($persist !== false) {
            $vacancy->setTitle($data['title']);
            $vacancy->setDescription($data['description']);

            $this->entityManager->flush();
        }

        return $vacancy;
    }
}