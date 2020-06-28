<?php

namespace App\Controller;

use App\Entity\Site;
use App\Entity\Vacancy;
use App\Repository\VacancyRepository;
use App\Service\DouParser;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VacancyController extends AbstractController
{
    /** @var DouParser $parser */
    protected $parser;

    /**
     * VacancyController constructor.
     * @param DouParser $parser
     */
    public function __construct(DouParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @Route("/vacancy", name="vacancy")
     * @return Response
     */
    public function index()
    {
        /** @var ObjectManager $entityManager */
        $entityManager = $this->getDoctrine()->getManager();
        /** @var VacancyRepository $repository */
        $repository = $entityManager->getRepository(Vacancy::class);
        /** @var Site $site */
        $site = $entityManager->getRepository(Site::class)->find(1);
        $url = $site->getItemListUrl();
//        $vacancyLinks = $this->parser->parseItemList($url);
        $vacancyLinks = [];

        foreach ($vacancyLinks as $link) {
            $repository->createOrUpdateByUrl($link, $site);
        }

        $title = 'Vacancy list';

        /** @var Vacancy[] $vacancy */
        $vacancies = $repository->findAll();

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

        $url = $vacancy->getUrl();
        $data = $this->parser->parseItem($url);

        $vacancy->setTitle($data['title']);
        $vacancy->setDescription($data['description']);
        $entityManager->flush();

        return $this->redirectToRoute('vacancy_show', [
            'id' => $vacancy->getId(),
        ]);
    }
}
