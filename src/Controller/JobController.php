<?php

namespace App\Controller;

use App\Entity\CategoryJob;
use App\Entity\Job;
use App\Repository\CategoryJobRepository;
use App\Repository\JobRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobController extends AbstractController
{
    public function __construct(
    private JobRepository $jobRepo,
    private CategoryJobRepository $categoryJobRepo
        
    )
    {
        
    }

    #[Route('/offres-emplois', name: 'app_jobs')]
    public function jobs(): Response
    {   
        //$this->jobRepo->listAllJobs(new \DateTimeImmutable('now'))
        return $this->render('job_template/jobs.html.twig', [
            "jobs"=>$this->jobRepo->findAll()
        ]);
    }

    #[Route('/offres-emplois/toutes-nos-secteur-activites', name: 'app_category_job')]
    public function allCategoriesJob(): Response
    {   
        return $this->render('job_template/all-category-job.html.twig', [
            "categoriesJob"=>$this->categoryJobRepo->listAllCategoriesJobByDate()
        ]);
    }

    #[Route('/offres-emplois/secteur-activites/{slug}', name: 'app_job_by_category')]
    public function jobsByCategory(CategoryJob $categoryJob): Response
    {   
        // dd($this->jobRepo->listJobsByCategory($categoryJob->getId()));
        return $this->render('job_template/job-by-category.html.twig', [
            "jobs"=>$this->jobRepo->listJobsByCategory($categoryJob->getId())
        ]);
    }

    #[Route('/offres-emplois/{slug}', name: 'app_job_detail')]
    public function jobDetail(Job $job): Response
    {
        return $this->render('job_template/job-single.html.twig', [
            "job"=>$job,
            "recentJobs"=>$this->jobRepo->recentJob(),
            "categoriesJob"=>$this->categoryJobRepo->listCategories()
        ]);
    }
}
