<?php

namespace App\Repository;


use App\Entity\Site;
use App\Entity\Vacancy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * @method Vacancy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vacancy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vacancy[]    findAll()
 * @method Vacancy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VacancyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vacancy::class);
    }

    public function findOneByIdJoinedToSite($siteId)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT v, s
            FROM App\Entity\Vacancy v
            INNER JOIN v.site s
            WHERE v.id = :id'
        )->setParameter('id', $siteId);

        return $query->getOneOrNullResult();
    }

    /**
     * @param $title
     * @return Vacancy[] Returns an array of Vacancy objects
     */
    public function findByTitle($title)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.title = :val')
            ->setParameter('val', $title)
            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findOneByUrl($url): ?Vacancy
    {
        $vacancy = null;

        try {
            $vacancy = $this->createQueryBuilder('v')
                ->andWhere('v.url = :val')
                ->setParameter('val', $url)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            // todo handle duplicates
        }

        return $vacancy;
    }

    public function createOrUpdateByUrl($url, Site $site = null): Vacancy
    {
        $vacancy = null;

        try {
            $vacancy = $this->createQueryBuilder('v')
                ->andWhere('v.url = :val')
                ->setParameter('val', $url)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            // todo remove duplicates
        }

        /** @var ObjectManager $entityManager */
        $entityManager = $this->getEntityManager();

        /** @var SiteRepository $siteRepository */
        $siteRepository = $entityManager->getRepository(Site::class);

        if (!$vacancy) {
            $vacancy = new Vacancy();
            $vacancy->setUrl($url);

            $entityManager->persist($vacancy);
            $entityManager->flush();
        } else {
            // todo update
        }

        if ($site) {
            $vacancy->setSite($site);
            $site->addVacancy($vacancy);

            $entityManager->persist($vacancy);
            $entityManager->persist($site);
            $entityManager->flush();
        }

        return $vacancy;
    }

}
