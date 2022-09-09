<?php

namespace App\Controller\Admin;

use App\Entity\Job;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

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
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig')
            ->setEntityLabelInSingular('une offre d\'emploi')
            ->setEntityLabelInPlural(' des offres d\'emplois')
            ->setSearchFields(['title', 'adresses.country', 'adresses.city'])
            ->setDefaultSort(['title' => 'ASC', 'createdAt' => 'DESC'])
            ->setPaginatorPageSize(30)
            ;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title')
                ->setLabel("Titre")
                ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
            AssociationField::new('categoryJob')
                ->setLabel("Secteur d'activité")
                ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
            TextField::new('jobLevel')
                ->setLabel("Niveau d'étude")
                ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
            AssociationField::new('adresses')
                ->setLabel('Localisation')
                ->hideOnIndex()
                ->formatValue(function ($value, $entity) {
                return implode(",",$entity->getAdresses()->toArray());
                })
                ->setTemplatePath('admin/renderAdresseTemplate.html.twig')
                ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
            ImageField::new('image')
                ->setLabel('Image')
                ->setBasePath('uploads/images/jobImages')
                ->setUploadDir('public/uploads/images/jobImages')
                ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
            DateField::new('expiredAt')
                ->setLabel("Date d'expiration")
                ->setFormat(DateTimeField::FORMAT_SHORT)
                ->setTimezone('Africa/Malabo')
                ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
            
            TextareaField::new('description')
                ->setLabel('Description de l\'offre')
                ->setFormType (CKEditorType::class)
                ->hideOnIndex()
                ->renderAsHtml ()
                ->setDefaultColumns('col-12 col-md-12 col-xxl-12'),
            
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

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('title')
            ->add('jobLevel')
            ->add(EntityFilter::new('adresses'))
            ->add(EntityFilter::new('categoryJob'))
        ;
    }
    
}
