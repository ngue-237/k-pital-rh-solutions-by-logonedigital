<?php

namespace App\Entity;

use App\Repository\CanditureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CanditureRepository::class)]
class Canditure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isHired = null;

    #[ORM\ManyToOne(inversedBy: 'canditures')]
    private ?job $jobs = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsHired(): ?bool
    {
        return $this->isHired;
    }

    public function setIsHired(?bool $isHired): self
    {
        $this->isHired = $isHired;

        return $this;
    }

    public function getJobs(): ?job
    {
        return $this->jobs;
    }

    public function setJobs(?job $jobs): self
    {
        $this->jobs = $jobs;

        return $this;
    }
}
