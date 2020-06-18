<?php


namespace App\Controller;


use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    public function index()
    {
        $title = 'Vacancy list';
        $vacancies = [
            ['title' => 'vacancy_1'],
            ['title' => 'vacancy_2'],
        ];

        return $this->render('base.html.twig', compact('title', 'vacancies'));
    }

    public function xxx(LoggerInterface $logger)
    {
        $url = $this->generateUrl('xxx', ['max' => 10]);

        return new Response('123');
    }
}