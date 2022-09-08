<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\JobRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ORM\Table(name: "jobs")]
#[UniqueEntity(fields: ['title'], message: "Cette offre d'emploi existe déjà")]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expiredAt = null;

    #[ORM\Column(length: 255)]
    private ?string $jobLevel = null;

    #[ORM\Column(length: 255)]
    private ?string $ref = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Adresse::class, inversedBy: 'jobs')]
    private Collection $adresses;

    #[ORM\OneToMany(mappedBy: 'jobs', targetEntity: CategoryJob::class)]
    private Collection $categoryJobs;

    public function __construct()
    {
        $this->adresses = new ArrayCollection();
        $this->categoryJobs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(?\DateTimeImmutable $expiredAt): self
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    public function getJobLevel(): ?string
    {
        return $this->jobLevel;
    }

    public function setJobLevel(string $jobLevel): self
    {
        $this->jobLevel = $jobLevel;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Adresse>
     */
    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

    public function addAdress(Adresse $adress): self
    {
        if (!$this->adresses->contains($adress)) {
            $this->adresses->add($adress);
        }

        return $this;
    }

    public function removeAdress(Adresse $adress): self
    {
        $this->adresses->removeElement($adress);

        return $this;
    }

    /**
     * @return Collection<int, CategoryJob>
     */
    public function getCategoryJobs(): Collection
    {
        return $this->categoryJobs;
    }

    public function addCategoryJob(CategoryJob $categoryJob): self
    {
        if (!$this->categoryJobs->contains($categoryJob)) {
            $this->categoryJobs->add($categoryJob);
            $categoryJob->setJobs($this);
        }

        return $this;
    }

    public function removeCategoryJob(CategoryJob $categoryJob): self
    {
        if ($this->categoryJobs->removeElement($categoryJob)) {
            // set the owning side to null (unless already changed)
            if ($categoryJob->getJobs() === $this) {
                $categoryJob->setJobs(null);
            }
        }

        return $this;
    }
}
