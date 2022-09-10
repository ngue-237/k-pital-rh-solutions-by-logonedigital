<?php

namespace App\Entity;

use App\Entity\Job;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryJobRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CategoryJobRepository::class)]
#[ORM\Table(name: "CategoriesJobs")]
#[UniqueEntity(fields: ['designation'], message: "Ce secteur d'activité existe déjà.")]
class CategoryJob
{
    public function __toString()
    {
        return $this->designation;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $designation = null;

    #[ORM\ManyToOne(inversedBy: 'categoryJobs')]
    private ?job $jobs = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getJobs(): ?job
    {
        return $this->jobs;
    }

    public function setJobs(?Job $jobs): self
    {
        $this->jobs = $jobs;

        return $this;
    }
}
