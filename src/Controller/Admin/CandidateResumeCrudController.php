<?php

namespace App\Controller\Admin;

use App\Entity\CandidateResume;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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


    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new ('photo'),
            TextField::new('nomcomplet','Nom et Prénom'),
            TelephoneField::new ('telephone','Numéro de Telephone'),
            TextField::new ('email','Email'),
            TextEditorField::new('presentation','Présentation'),
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
