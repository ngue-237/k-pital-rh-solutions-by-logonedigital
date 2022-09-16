<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Candidature;
use App\Entity\CategoryJob;
use App\Form\CandidatureType;
use App\Repository\JobRepository;
use App\Services\FileUploader;
use Symfony\Component\Asset\UrlPackage;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryJobRepository;
use Sonata\SeoBundle\Seo\SeoPageInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\VersionStrategy\StaticVersionStrategy;

class JobController extends AbstractController
{
    public function __construct(
    private JobRepository $jobRepo,
    private CategoryJobRepository $categoryJobRepo,
    private PaginatorInterface $paginator,
    private EntityManagerInterface $em,
    private SeoPageInterface $seoPage,
    private UrlGeneratorInterface $urlGenerator,
    private CacheInterface $cache ,     
    )
    {
        
    }

    #[Route('/offres-emploi', name: 'app_jobs')]
    public function jobs(Request $request): Response
    {   
        

        /**FILTER PART */
        $filters = $request->get('categories');
        $adresseFilter = $request->get('adresse');  
        if($request->get('ajax')){
           
            if($filters !=null and $adresseFilter == null){
                $jobs = $this->paginator->paginate($this->jobRepo->listAllJobs(new \DateTimeImmutable('now'), $filters, $adresseFilter), $request->query->getInt('page', 1), 9);
                return new JsonResponse([
                    "content"=>$this->renderView('job_template/jobsList.html.twig',compact('jobs'))
                ]);
            }
            else if($filters == null and $adresseFilter != null){
                
                $jobs = $this->paginator->paginate($this->jobRepo->listAllJobs(new \DateTimeImmutable('now'), $filters, $adresseFilter), $request->query->getInt('page', 1), 9);
                return new JsonResponse([
                    "content"=>$this->renderView('job_template/jobsList.html.twig',compact('jobs'))
                ]);
            }
            else if($filters != null and $adresseFilter != null){

                $jobs = $this->paginator->paginate($this->jobRepo->listAllJobs(new \DateTimeImmutable('now'), $filters, $adresseFilter), $request->query->getInt('page', 1), 9);
                return new JsonResponse([
                    "content"=>$this->renderView('job_template/jobsList.html.twig',compact('jobs'))
                ]);
            }

            else{

            $jobs = $this->paginator->paginate($this->jobRepo->listAllJobs(new \DateTimeImmutable('now'), $filters, $adresseFilter), $request->query->getInt('page', 1), 9);

            return new JsonResponse([
                "content"=>$this->renderView('job_template/jobsList.html.twig',compact('jobs'))
            ]);
        }

        }
        /** END FILTER PART */

        /**SEO PART */
        $this->seoJobsPage();
        /**END SEO PART */
        
        $adresses = $this->em->createQuery('SELECT c from App\Entity\Adresse c ORDER BY c.city ASC')
        ->setCacheMode(\Doctrine\ORM\Cache::MODE_GET)
        ->setCacheable(true)
        ->setLifetime(86400)
        ->getResult();

        $categoriesJob = $this->em->createQuery('SELECT c from App\Entity\CategoryJob c ORDER BY c.designation ASC')
        ->setCacheMode(\Doctrine\ORM\Cache::MODE_GET)
        ->setCacheable(true)
        ->setLifetime(86400)
        ->getResult();

        return $this->render('job_template/jobs.html.twig', [
            "jobs"=>$this->paginator->paginate($this->jobRepo->listAllJobs(new \DateTimeImmutable('now'), $filters, $adresseFilter), $request->query->getInt('page', 1), 9),
            "categoriesJob"=>$categoriesJob,
            "adresses"=> $adresses
        ]);
        
    }
    /**
     * managing seo for jobs page
     *
     * @return void
     */
    private function seoJobsPage(){
        /**SEO PART */
        $description = "la meilleures agence de conseils Rh au Cameroun";
        $this -> seoPage -> setTitle ("Toutes nos offres d'emplois")
            ->addMeta ('property','og:title','les petites annonces MA.BA.CE II')
            ->addTitleSuffix("CAPITAL RH SOLUTIONS")
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:title', "Toutes nos offres d'emplois-CAPITAL RH SOLUTIONS")
            ->setLinkCanonical($this->urlGenerator->generate('app_jobs',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_jobs',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$description)
            ->setBreadcrumb('/Offres-emplois/toutes-nos-offres', []);
        /**END SEO PART */
    }

    #[Route('/offres-emplois/toutes-nos-secteur-activites', name: 'app_category_job')]
    public function allCategoriesJob(Request $request): Response
    {   
        
        /**SEO PART */
        $this->seoCategoryJobPage();
        /**END SEO PART */

        return $this->render('job_template/all-category-job.html.twig', [
            "categoriesJob"=>$this->paginator->paginate($this->categoryJobRepo->listAllCategoriesJobByDate(), $request->query->getInt('page', 1), 9)
        ]);
    }

    private function seoCategoryJobPage():void{
        $description = "la meilleures agence de conseils Rh au Cameroun";
        $this -> seoPage -> setTitle ("Toutes les secteurs d'activité dans lesquels nous internons")
            ->addMeta ('property','og:title','les petites annonces MA.BA.CE II')
            ->addTitleSuffix("CAPITAL RH SOLUTIONS")
            ->addMeta('name', 'description', $description)
            ->addMeta('property', 'og:title', "Toutes nos offres d'emplois-CAPITAL RH SOLUTIONS")
            ->setLinkCanonical($this->urlGenerator->generate('app_category_job',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_category_job',[], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$description)
            ->setBreadcrumb('/Offres-emplois/toutes-secteurs-activité', []);
    }

    #[Route('/offres-emplois/secteur-activites/{slug}', name: 'app_job_by_category')]
    public function jobsByCategory(CategoryJob $categoryJob, Request $request): Response
    { 
        $jobsCached = $this->paginator->paginate($this->jobRepo->listJobsByCategory($categoryJob->getId()), $request->query->getInt('page', 1), 9); 
       
         /** SEO PART */
        $this->seoSingleCategoryJobPage($categoryJob);
        /** END SEO PART */

        return $this->render('job_template/job-by-category.html.twig', [
            "jobs"=>$jobsCached,
            "categoryJob"=>$categoryJob
        ]);
    }

    private function seoSingleCategoryJobPage($categoryJob){
        $this->seoPage->setTitle ($categoryJob->getDesignation())
            -> addMeta ('property','og:title',$categoryJob->getDesignation())
            ->addMeta('name', 'description', $categoryJob->getDescription())
            ->addTitleSuffix("CAPITAL RH SOLUTIONS")
            ->addMeta('property', 'og:title', $categoryJob->getDesignation())
            ->setLinkCanonical($this->urlGenerator->generate('app_job_by_category',['slug'=>$categoryJob->getSlug()], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_job_by_category',['slug'=>$categoryJob->getSlug()], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$categoryJob->getDesignation())
            ->setBreadcrumb('blog', ['post' => $categoryJob]);
    }

    #[Route('/offres-emplois/{slug}', name: 'app_job_detail')]
    public function jobDetail(Request $request, Job $job, FileUploader $fileUploader): Response
    {
        //$jobCached = $job;

        $jobsCached=$this->em->createQuery("SELECT j FROM App\Entity\Job j WHERE  j.slug = :slug")
        ->setParameter("slug", $job->getSlug ())
        ->setCacheMode(\Doctrine\ORM\Cache::MODE_GET)
        ->setCacheable(true)
        ->setLifetime(86400)
        ->getResult()
        ;

        if(!$jobsCached){
            $this->createNotFoundException();
        }


        /** SEO PART */
        foreach($jobsCached as $jobCached){
            $this->singleJobSeo($jobCached);
        }
        /** END SEO PART */

        $candidature = new Candidature();

        $candidateForm =  $this->createForm (CandidatureType::class, $candidature);
        $candidateForm->handleRequest ($request);

        if($candidateForm->isSubmitted ()){
            if($candidateForm->isValid ()){
                $cvFile = $candidateForm->get('cv')->getData();
                if ($cvFile) {
                    $cvFileName = $fileUploader->upload($cvFile,'cvs_directory');
                    $candidature->setCv($cvFileName);
                }

                $candidature->setJob ($job);
                $candidature->setUser ($this->getUser ());
                $candidature->setCandidateResume ($this->getUser ()->getCandidateResume());
                $candidature->setExpiredAt ($job->getExpiredAt ());
                $candidature->setCreatedAt (new \DateTimeImmutable('now'));
                $candidature->setIsHired (0);
                $candidature->setMessage ($candidateForm->get ('message')->getData ());

                dd ($candidature);
                $this->em->persist ($candidature);
                $this->em->flush ();

                return $this->redirectToRoute ('app_jobs');
            }
        }


        return $this->render('job_template/job-single.html.twig', [
            "jobs"=>$jobsCached,
            "recentJobs"=>$this->jobRepo->recentJob(),
            "categoriesJob"=>$this->categoryJobRepo->listCategories(),
            "form"=>$candidateForm->createView(),
        ]);
    }

    private function singleJobSeo(Job $jobCached){
        $urlPackage = new UrlPackage(
            'https://capitalrhsolutions.com/uploads/images/jobImages/'.$jobCached->getImage(),
            new StaticVersionStrategy('v1')
        );

        $urlPackage->getUrl($jobCached->getImage());
        $this->seoPage->setTitle ($jobCached->getTitle())
            -> addMeta ('property','og:title',$jobCached->getTitle())
            ->addMeta('name', 'description', $jobCached->getDescription())
            ->addTitleSuffix("CAPITAL RH SOLUTIONS")
            ->addMeta('property', 'og:title', $jobCached->getTitle())
            ->setLinkCanonical($this->urlGenerator->generate('app_job_detail',['slug'=>$jobCached->getSlug()], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:url',  $this->urlGenerator->generate('app_job_detail',['slug'=>$jobCached->getSlug()], urlGeneratorInterface::ABSOLUTE_URL))
            ->addMeta('property', 'og:description',$jobCached->getTitle())
            ->addMeta('property', 'og:image',$urlPackage->getBaseUrl($jobCached->getImage()))
            ->addMeta('property', 'og:image:width',"300")
            ->addMeta('property', 'og:image:height',"300")
           ->setBreadcrumb('blog', ['post' => $jobCached]);
    }

    /**SEARCH ENGINE ON JOBS */
    #[Route( '/offres-emplois-2' , name : 'app_job_search' )]
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

    /**FILTER ENGINE */
     #[Route( '/offre-emplois-2/secteurs-activite' , name : 'app_advance_filter' )]
    public function advanceFilter ( Request $request )
    {   
        $data = $this->categoryJobRepo->searchCategory($request->get('searchValues'));
        //dd($this->jobRepo->find($request->get('searchValues')));
            return $this -> render ( 'job_template/jobsList.html.twig' , [
                'jobs' => $this->paginator->paginate($this->jobRepo->advanceFilter($request->get('searchValues'), new \DateTimeImmutable('now')), $request->query->getInt('page', 1), 9),
            ] );
    }
}
