<?php
namespace App\Twig;

use App\Repository\CategoryJobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategoryJobExtension extends AbstractExtension{

public function __construct(private CategoryJobRepository  $catJobRepo, private EntityManagerInterface $em)
{
    
}

public function getFunctions() : array
{
    return [
        new TwigFunction("catgsJob", [$this, "getCategoriesJob"])
    ];
}

public function getCategoriesJob(){
    return $this->catJobRepo->findBy([],['designation'=>"ASC"], 5);
}
    

}