<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', "GERER VOS CANDIDATURES")
            ->setPageTitle('detail', "DETAIL DELA CANDIDATURE")
            ->setPageTitle('edit', "MODIFIER UNE CANDIDATURE")
            ->setEntityLabelInSingular('une candidature')
            ->setEntityLabelInPlural(' des candidatures')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPaginatorPageSize(30)
            ;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')
            ->setLabel("Nom Complet")
            ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
            EmailField::new('email')
            ->setLabel('Email')
            ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
            TextField::new('subject')
            ->setLabel("objet")
            ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
            DateField::new('createdAt')
            ->setLabel("Date de création")
            ->hideOnForm()
            ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
            TextareaField::new('message')
            ->setLabel("Contenu")
            ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
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
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
            ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('email')
            ->add(DateTimeFilter::new("createdAt"))
        ;
    }
}
