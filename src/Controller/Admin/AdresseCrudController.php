<?php

namespace App\Controller\Admin;

use App\Entity\Adresse;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AdresseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Adresse::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            CountryField::new('country')->setLabel("Pays"),
            TextField::new('city')->setLabel("Ville"),
            TextField::new('region')->setLabel("Région"),
            TextField::new('postaleCode')->setLabel("Code postale"),
            AssociationField::new('jobs')
                ->setLabel('offre(s) d\'emploi(s)')
                ->hideOnIndex()
                ->hideOnForm()
                ->formatValue(function ($value, $entity) {
                return implode(",",$entity->getJobs()->toArray());
                })
                ->setTemplatePath('admin/renderAdresseTemplate.html.twig'),
        ];
    }
    

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', "GERER VOS ADRESSES")
            ->setPageTitle('new', "AJOUTER UNE ADRESSE")
            ->setPageTitle('detail', "CONSULTER VOS ADRESSES")
            ->setPageTitle('edit', "MODIFIER UNE ADRESSE")
            ->setEntityLabelInSingular('une adresse ')
            ->setEntityLabelInPlural(' des adresses ')
            ->setSearchFields(['country', 'city', 'postaleCode'])
            ->setDefaultSort(['country' => 'ASC', 'city' => 'ASC'])
            ->setPaginatorPageSize(30)
            ;
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

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('country')
            ->add('city')
            ->add('postaleCode')
        ;

    }
}
