<?php

namespace App\Controller\Admin;

use App\Entity\Job;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class JobCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Job::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', "GERER VOS OFFRES D'EMPLOIS")
            ->setPageTitle('new', "AJOUTER UNE OFFRE D'EMPLOIS")
            ->setPageTitle('detail', "CONSULTER VOS OFFRES D'EMPLOIS")
            ->setPageTitle('edit', "MODIFIER UNE OFFRE D'EMPLOI")
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
            ;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title')
                ->setLabel("Titre"),
            AssociationField::new('categoryJobs')
                ->setLabel("Secteur d'activité"),
            TextField::new('jobLevel')
                ->setLabel("Niveau d'étude"),
            AssociationField::new('adresses')
                ->setLabel('Localisation')
                ->setLabel("Choisir une adresse"),
                
            DateTimeField::new('expiredAt')
                ->setLabel("Date d'expiration"),
            TextareaField::new('description')
                ->setLabel('Description de l\'offre')
                ->setFormType (CKEditorType::class)
                ->renderAsHtml (),
        ];
    }

    

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ;
    }
    
}
