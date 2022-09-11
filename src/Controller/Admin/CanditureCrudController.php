<?php

namespace App\Controller\Admin;

use App\Entity\Canditure;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class CanditureCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Canditure::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('jobs')
                ->setLabel("Secteur d'activité")
                ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
        ];
    }
    
}
