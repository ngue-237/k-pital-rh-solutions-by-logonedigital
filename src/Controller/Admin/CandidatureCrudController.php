<?php

namespace App\Controller\Admin;

use App\Admin\Field\PdfField;
use App\Entity\Candidature;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class CandidatureCrudController extends AbstractCrudController
{

    public function configureActions ( Actions $actions ) : Actions
    {

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->disable(Action::NEW);
    }


    public static function getEntityFqcn(): string
    {
        return Candidature::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', "Gérer les candidatures ")
            ->setPageTitle('detail', "Candidature")
            ->setPageTitle('edit', "Candidature")
            ->setEntityLabelInSingular('candidature')
            ->setEntityLabelInPlural(' les candidatures')
            ->setSearchFields(['job.title', 'isHired', 'user.lastname'])
            ->setDefaultSort(['isHired' => 'ASC', 'createdAt' => 'DESC'])
            ->setPaginatorPageSize(30)
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('job')
                ->setLabel("L'offre d'emploi")
                ->setDefaultColumns('col-12 col-md-6 col-xxl-6'),
            AssociationField::new('candidateResume')
                ->setLabel("Candidats"),
            BooleanField::new ('isHired','Etat(accepté ou non)'),
            DateField::new('createdAt')
                ->setLabel("Date")
                ->setFormat(DateTimeField::FORMAT_SHORT)
                ->setTimezone('Africa/Malabo'),
            PdfField::new ('cv','Curriculum Vitae')->setTemplatePath ('vich/cv.html.twig'),

        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('createdAt')
            ->add('isHired')
            ->add(EntityFilter::new('candidateResume'))
            ->add(EntityFilter::new('job'))
            ;
    }
}
