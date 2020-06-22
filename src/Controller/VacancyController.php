<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Vacancy;
use App\Repository\VacancyRepository;
use App\Service\Parser;
use Doctrine\DBAL\DBALException;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VacancyController extends AbstractController
{
    /**
     * @Route("/vacancy", name="vacancy")
     * @param Parser $parser
     * @return Response
     */
    public function index()
    {
        /** @var ObjectManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        $title = 'Vacancy list';

        /** @var Vacancy[] $vacancy */
        $vacancies = $entityManager->getRepository(Vacancy::class)->findAll();

        /** @var Vacancy $vacancy */
        $vacancy = $entityManager->getRepository(Vacancy::class)->find(1);

        $sites = $entityManager->getRepository(Site::class)->findAll();

        /** @var Site $site */
        $site = $entityManager->getRepository(Site::class)->find(1);

        $vacancy->setSite($site);

        $entityManager->persist($vacancy);
        $entityManager->persist($site);
        $entityManager->flush();

        return $this->render('vacancy/index.html.twig', compact('title', 'vacancies'));
    }

    /**
     * @Route("/vacancy/create", name="vacancy_create")
     * @return Response
     * @throws Exception
     */
    public function create(): Response
    {
        /** @var ObjectManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        $sql = "select max(id) from vacancy;";

        try {
            $stmt = $entityManager->getConnection()->prepare($sql);
        } catch (DBALException $exception) {
            throw new Exception('Bad SQL query');
        }

        $stmt->execute();
        $result = $stmt->fetch();
        $id = ((int) array_shift($result)) + 1;

        $vacancy = new Vacancy();
        $vacancy->setUrl('vacancy_' . $id . '_url');

        $entityManager->persist($vacancy);
        $entityManager->flush();

        $url = $this->generateUrl('vacancy_show', ['id' => $vacancy->getId()]);

        $vacancy->setTitle('vacancy ' . $id . ' title');
        $vacancy->setDescription('vacancy ' . $id . ' description');
        $vacancy->setUrl($url);

        $entityManager->flush();

        return new Response('Vacancy created with ID = ' . $vacancy->getId(), Response::HTTP_OK);
    }

    /**
     * @Route("/vacancy/{id}", name="vacancy_show")
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        /** @var Vacancy $vacancy */
        $vacancy = $this->getDoctrine()->getRepository(Vacancy::class)->findOneByIdJoinedToSite($id);

        if (!$vacancy) {
            throw $this->createNotFoundException('No vacancy found for id ' . $id);
        }

        /** @var Site $site */
        $site = $vacancy->getSite();

        /** @var \Doctrine\ORM\PersistentCollection $vacancies */
        $vacancies = $site->getVacancies();

        return new Response($vacancy, Response::HTTP_OK);
    }

    /**
     * @Route("/vacancy/{id}/edit", name="vacancy_edit")
     * @param $id
     * @return Response
     */
    public function update($id)
    {
        /** @var ObjectManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        /** @var Vacancy|null $vacancy */
        $vacancy = $entityManager->getRepository(Vacancy::class)->find($id);

        if (!$vacancy) {
            throw $this->createNotFoundException('No vacancy found for id ' . $id);
        }

        $vacancy->setDescription('new description');
        $entityManager->flush();

        return $this->redirectToRoute('vacancy_show', [
            'id' => $vacancy->getId(),
        ]);
    }
}
