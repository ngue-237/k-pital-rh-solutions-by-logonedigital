<?php

namespace App\Entity;

use App\Repository\LanguageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LanguageRepository::class)]
class Language
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $languagewrite = null;

    #[ORM\Column(length: 32)]
    private ?string $languagespeak = null;

    #[ORM\ManyToOne(inversedBy: 'languages')]
    private ?CandidateResume $candidateResume = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLanguagewrite(): ?string
    {
        return $this->languagewrite;
    }

    public function setLanguagewrite(string $languagewrite): self
    {
        $this->languagewrite = $languagewrite;

        return $this;
    }

    public function getLanguagespeak(): ?string
    {
        return $this->languagespeak;
    }

    public function setLanguagespeak(string $languagespeak): self
    {
        $this->languagespeak = $languagespeak;

        return $this;
    }

    public function getCandidateResume(): ?CandidateResume
    {
        return $this->candidateResume;
    }

    public function setCandidateResume(?CandidateResume $candidateResume): self
    {
        $this->candidateResume = $candidateResume;

        return $this;
    }
}
