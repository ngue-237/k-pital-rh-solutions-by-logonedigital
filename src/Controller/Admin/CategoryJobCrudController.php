<?php

namespace App\Controller\Admin;

use App\Entity\CategoryJob;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class CategoryJobCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CategoryJob::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('designation')->setLabel("Secteur d'activité")
            ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
            TextareaField::new('description')->setLabel("Description")
            ->setDefaultColumns('col-12 col-md-6 col-xxl-6')
            ->setRequired(true),
            AssociationField::new('jobs')
                ->setLabel('offre(s) d\'emploi(s)')
                ->hideOnIndex()
                ->hideOnForm()
                ->formatValue(function ($value, $entity) {
                return implode(",",$entity->getJobs()->toArray());
                })
                ->setTemplatePath('admin/renderAdresseTemplate.html.twig')
                ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
        ];
    }

     public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', "GESTION DES SECTEURS D'ACTIVITES")
            ->setPageTitle('new', "AJOUTER UN SECTEURS D'ACTIVITES")
            ->setPageTitle('detail', "CONSULTER VOS SECTEURS D'ACTIVITES")
            ->setPageTitle('edit', "MODIFIER UN SECTEURS D'ACTIVITES")
            ->setEntityLabelInSingular('un nouveau secteur d\'activité')
            ->setEntityLabelInPlural(' des nouveaux secteurs d\'activités')
            ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('designation')
        ;

    }
    
}
