<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SiteRepository::class)
 */
class Site
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $domain;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Parser", inversedBy="sites")
     */
    private $parser;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vacancy", mappedBy="site", orphanRemoval=true)
     */
    private $vacancies;

    /**
     * Site constructor.
     */
    public function __construct()
    {
        $this->vacancies = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return $this
     */
    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Parser|null
     */
    public function getParser(): ?Parser
    {
        return $this->parser;
    }

    /**
     * @param Parser $parser
     * @return $this
     */
    public function setParser(Parser $parser): self
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getVacancies(): Collection
    {
        return $this->vacancies;
    }

    /**
     * @param Vacancy $vacancy
     * @return $this
     */
    public function addVacancy(Vacancy $vacancy): self
    {
        if (!$this->vacancies->contains($vacancy)) {
            $this->vacancies[] = $vacancy;
            $vacancy->setSite($this);
        }

        return $this;
    }

    /**
     * @param Vacancy $vacancy
     * @return $this
     */
    public function removeVacancy(Vacancy $vacancy): self
    {
        if ($this->vacancies->contains($vacancy)) {
            $this->vacancies->removeElement($vacancy);

            // set the owning side to null (unless already changed)
            if ($vacancy->getSite() === $this) {
                $vacancy->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return __CLASS__ . ': ' . json_encode(get_object_vars($this));
    }
}
