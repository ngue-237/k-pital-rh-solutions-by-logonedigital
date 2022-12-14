<?php

namespace App\Controller;

use App\Repository\CategoryJobRepository;
use App\Repository\JobRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{

    public function __construct(
    private JobRepository $jobRepo,
    private CategoryJobRepository $categoryJobRepo
    )
    {}

    #[Route('/sitemap.xml', name: 'app_sitemap', defaults: ["_format"=>"xml"])]
    public function index(Request $request): Response
    {
        $hostname = $request->getSchemeAndHttpHost();
        $urls = [];

        $urls []= ['loc'=> $this->generateUrl("app_home"),"priority"=>0.9];
        $urls []= ['loc'=> $this->generateUrl("app_about"),"priority"=>0.8];
        $urls []= ['loc'=> $this->generateUrl("app_services"),"priority"=>0.9];
        $urls []= ['loc'=> $this->generateUrl("app_cgu"),"priority"=>0.7];
        $urls []= ['loc'=> $this->generateUrl("app_contact"),"priority"=>0.8];
        $urls []= ['loc'=> $this->generateUrl("app_jobs"),"priority"=>0.9,];
        $urls []= ['loc'=> $this->generateUrl("app_category_job"),"priority"=>0.9,];
        
        //ajout des urls dynamique
        foreach($this->jobRepo->findAll() as $job){
            $images = [
                "loc" => "/public/uploads/images/jobImages/".$job->getImage(),
                "title"=>$job->getSlug()
            ];
            $urls []= [
                "loc"=>$this->generateUrl("app_job_detail", [
                    "slug"=>$job->getSlug()
                ]),
                'images' => $images,
                "priority"=>0.9,
                "changefreq"=>"monthly",
                "lastmod" =>$job->getUpdatedAt() ? $job->getUpdatedAt()->format('Y-m-d') :$job->getCreatedAt()->format('Y-m-d')
            ];
        }
        foreach($this->categoryJobRepo->findAll() as $category){
            $urls []= [
                "loc"=>$this->generateUrl("app_job_by_category", [
                    "slug"=>$category->getSlug()
                ]),
                "priority"=>0.9,
                "changefreq"=>"monthly",
                "lastmod" =>$category->getUpdatedAt() ? $category->getUpdatedAt()->format('Y-m-d') :$category->getCreatedAt()->format('Y-m-d')
            ];
        }

        $response = new Response(
            $this->renderView('sitemap/index.html.twig', ['urls' => $urls,
                'hostname' => $hostname]),
            200
        );

        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
