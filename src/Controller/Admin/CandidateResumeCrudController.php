<?php

namespace App\Controller\Admin;

use App\Admin\Field\PdfField;
use App\Entity\CandidateResume;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CandidateResumeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CandidateResume::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', "Les profils des candidats")
            ->setPageTitle('detail', "Profil")
            ->setPageTitle('edit', "Profil")
            ->setEntityLabelInSingular('Profil')
            ->setEntityLabelInPlural(' les profils des candidats')
            ->setPaginatorPageSize(30)
            ;
    }


    public function configureFields(string $pageName): iterable
    {
       // $this->getContext ()->getEntity ()->getInstance ();
        return [
            ImageField::new ('photo')->setBasePath ('/uploads/images/profilImages/'),
            TextField::new('nomcomplet','Nom et Prénom'),
            TelephoneField::new ('telephone','Numéro de Telephone'),
            EmailField::new ('email','Email'),
            TextEditorField::new('presentation','Présentation'),
            PdfField::new ('cv','Curriculum Vitae')
            ->setTemplatePath ('vich/cv.html.twig'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->disable(Action::NEW)
            ;
    }

}
