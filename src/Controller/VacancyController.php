<?php

namespace App\Controller;

use App\Entity\Vacancy;
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
     */
    public function index()
    {
        $title = 'Vacancy list';
        /** @var Vacancy[] $vacancy */
        $vacancies = $this->getDoctrine()->getRepository(Vacancy::class)->findAll();

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
        $vacancy = $this->getDoctrine()->getRepository(Vacancy::class)->find($id);

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
