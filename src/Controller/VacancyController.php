<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Vacancy;
use App\Service\VacancyProvider;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VacancyController extends AbstractController
{
    /** @var VacancyProvider $vacancyProvider */
    protected $vacancyProvider;

    /**
     * VacancyController constructor.
     * @param VacancyProvider $vacancyProvider
     */
    public function __construct(VacancyProvider $vacancyProvider)
    {
        $this->vacancyProvider = $vacancyProvider;
    }

    /**
     * @Route("/vacancy", name="vacancy")
     * @return Response
     */
    public function index()
    {
        /** @var ObjectManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();

        /** @var Site $site */
        $site = $entityManager->getRepository(Site::class)->find(1);

        $title = 'Vacancy list';
        $vacancies = $this->vacancyProvider->getVacancyListFromSite($site, true);

        return $this->render('vacancy/index.html.twig', compact('title', 'vacancies'));
    }

    /**
     * @Route("/vacancy/{id}", name="vacancy_show")
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        if (!is_numeric($id)) {
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        /** @var Vacancy $vacancy */
        $vacancy = $this->getDoctrine()->getRepository(Vacancy::class)->findOneByIdJoinedToSite($id);

        if (!$vacancy) {
            throw $this->createNotFoundException('No vacancy found for id ' . $id);
        }

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
        $vacancy = $entityManager->getRepository(Vacancy::class)->findOneByIdJoinedToSite($id);

        if (!$vacancy) {
            throw $this->createNotFoundException('No vacancy found for id ' . $id);
        }

        $vacancy = $this->vacancyProvider->getVacancyFromSite($vacancy, true);

        return $this->redirectToRoute('vacancy_show', [
            'id' => $vacancy->getId(),
        ]);
    }
}
