<?php

namespace App\Entity;

use App\Repository\ParserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParserRepository::class)
 */
class Parser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $class;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Site", mappedBy="parser", orphanRemoval=true)
     */
    private $sites;

    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->sites = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }
}
