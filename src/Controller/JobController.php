<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\CategoryJob;
use App\Repository\JobRepository;
use App\Repository\CategoryJobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class JobController extends AbstractController
{
    public function __construct(
    private JobRepository $jobRepo,
    private CategoryJobRepository $categoryJobRepo,
    private PaginatorInterface $paginator,
    private EntityManagerInterface $em
        
    )
    {
        
    }

    #[Route('/offres-emploi', name: 'app_jobs')]
    public function jobs(Request $request): Response
    {   
        //$this->jobRepo->listAllJobs(new \DateTimeImmutable('now'))
        
        
               
        return $this->render('job_template/jobs.html.twig', [
            "jobs"=>$this->paginator->paginate($this->jobRepo->listAllJobs(new \DateTimeImmutable('now')), $request->query->getInt('page', 1), 9),
            "categoriesJob"=>$this->em->createQuery('SELECT c from App\Entity\CategoryJob c ORDER BY c.designation ASC')->execute(),
            "adresses"=> $this->em->createQuery('SELECT c from App\Entity\Adresse c ORDER BY c.city ASC')->execute()

        ]);
    }

    #[Route('/offres-emplois/toutes-nos-secteur-activites', name: 'app_category_job')]
    public function allCategoriesJob(Request $request): Response
    {   
        
        return $this->render('job_template/all-category-job.html.twig', [
            "categoriesJob"=>$this->paginator->paginate($this->categoryJobRepo->listAllCategoriesJobByDate(), $request->query->getInt('page', 1), 9)
        ]);
    }

    #[Route('/offres-emplois/secteur-activites/{slug}', name: 'app_job_by_category')]
    public function jobsByCategory(CategoryJob $categoryJob): Response
    { 
       
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

    /**SEARCH ENGINE ON JOBS */

    #[Route( '/offres-emplois' , name : 'app_job_search' )]
    public function searchedJobs ( Request $request )
    {
            return $this -> render ( 'job_template/jobsList.html.twig' , [
                'jobs' => $this->paginator->paginate($this->jobRepo->jobSearch($request -> get ( 'searchValue' ),new \DateTimeImmutable('now')), $request->query->getInt('page', 1), 9),
            ] );
    }

    /**SEARCH ENGINE ON CATEGORIES JOB */
    #[Route( '/offre-emplois/secteurs-activite' , name : 'app_categoy_job_search' )]
    public function searchedCategoryJob ( Request $request )
    {   
            return $this -> render ( 'job_template/categoryJobList.html.twig' , [
                'categoriesJob' => $this->paginator->paginate($this->categoryJobRepo->searchCategory($request -> get ( 'searchValue' ) ), $request->query->getInt('page', 1), 9),
            ] );
    }
}
