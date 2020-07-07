<?php

namespace App\MessageHandler;

use App\Entity\Site;
use App\Message\ParseVacancyMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ParseVacancyMessageHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(ParseVacancyMessage $message)
    {
        $site = new Site();
        $site->setName('hello from ' . get_class($this));
        $site->setDomain(date('Y-m-d H:i:s'));

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $this->entityManager->persist($site);

        $this->entityManager->flush();

//        var_dump($message->getUrl());
//        die();
    }
}
