<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('lastname')->setLabel('Nom'),
            TextField::new('firstname')->setLabel('Prénom'),
            ChoiceField::new('roles')->setChoices([
                'User' => 'ROLE_USER',
                'Employes' => 'ROLE_ADMIN',
                'Administrateur' => 'ROLE_SUPER_ADMIN'
            ])
                ->allowMultipleChoices()
                ->autocomplete()
                ->setLabel('ROLE(S)'),
            EmailField::new('email')->setLabel('Email'),
            BooleanField::new('rgpd')->setLabel('RGPD')->hideOnIndex(),
            BooleanField::new('isVerified')->setLabel('COMPTE ACTIVE')->hideOnIndex(),
            BooleanField::new('isBlocked')->setLabel('BLOQUE')->hideOnIndex(),
            DateTimeField::new('createdAt')->setLabel('DATE DE CREATION')->hideOnForm()->hideOnIndex(),
            DateTimeField::new('updateAt')->setLabel('DERNIERE MISE A JOUR')->hideOnForm()->hideOnIndex()
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(DateTimeFilter::new('createdAt'))
            ->add(DateTimeFilter::new('updateAt'))
            ->add(BooleanFilter::new('isVerified'))
            ->add(BooleanFilter::new('isBlocked'))
            ;
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

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Gérer les utilisateurs')
            ->setPageTitle('new', 'Ajouter un utilisateur')
            ->setPageTitle('detail', "Utilisateur")
            ->setSearchFields(['lastname', 'firstname', 'email'])
            ->setEntityLabelInSingular('nouveau utilisateur')
            ->setEntityLabelInPlural('nouveaux utilisateurs')
            ->setAutofocusSearch();
    }

}
