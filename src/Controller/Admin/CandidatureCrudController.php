<?php

namespace App\Controller\Admin;

use App\Admin\Field\PdfField;
use App\Entity\CandidateResume;
use App\Entity\Candidature;
use App\Services\MailSender;
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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class CandidatureCrudController extends AbstractCrudController
{

    public function __construct (
        private EntityManagerInterface $entityManager,
        private MailSender $sender,
        private AdminUrlGenerator $adminUrlGenerator
    )
    {
    }

    public function configureActions ( Actions $actions ) : Actions
    {
        $hireCandidate = Action::new ('hireCandidate','Accepté la candidature')
            ->linkToCrudAction ('hireCandidate');
        $hireNotCandidate = Action::new ('hireNotCandidate','Réfusé la candidature')
            ->linkToCrudAction ('hireNotCandidate');

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add ('detail',$hireCandidate)
            ->add ('detail',$hireNotCandidate)
            ->disable(Action::NEW);
    }

    public function urlDispatcher(String $action){
        $url = $this->adminUrlGenerator
            ->setDashboard(DashboardController::class)
            ->setController(OrderCrudController::class)
            ->setAction($action)
            ->generateUrl();

        return $url;
    }

    public static function getEntityFqcn(): string
    {
        return Candidature::class;
    }

    public function hireCandidate(AdminContext $context){

        $candidature = $context->getEntity ()->getInstance ();
        $candidateResume = $this->entityManager->getRepository (CandidateResume::class)->find (
            $candidature->getCandidateResume()->getId()
        );

        if (!$candidateResume){
            throw $this->createNotFoundException ("Erreur! Ce profil n'exite pas!");
        }

        $candidature->setIsHired(true);

        $content = "Bonjour ".$candidateResume->getNomcomplet()."<br> Nous avons bien étudié votre candidature pour un poste de "
            .$candidature->getJob()->getTitle().' '."et nous vous en remercions.<br><br>";
        $content .="Nous avons donc le plaisir de vous informer que votre candidature a été retenue.";

        $this->sender->send(
            $candidateResume->getEmail(),
            $candidateResume->getNomcomplet(),
            $content,
            "Réponse à votre candidature chez CAPITAL RH"
        );

        $this->entityManager->flush ();

        $this->addFlash('notice',"Candidature mise à jour");
        return $this->redirect ($this->urlDispatcher (Action::INDEX));
    }

    public function hireNotCandidate(AdminContext $context){

        $candidature = $context->getEntity ()->getInstance ();
        $candidateResume = $this->entityManager->getRepository (CandidateResume::class)->find (
            $candidature->getCandidateResume()->getId()
        );

        if (!$candidateResume){
            throw $this->createNotFoundException ("Erreur! Ce profil n'exite pas!");
        }

        $candidature->setIsHired(false);

        $content = "Bonjour ".$candidateResume->getNomcomplet()."<br> Nous avons bien étudié votre candidature pour un poste de "
            .$candidature->getJob()->getTitle().' '."et nous vous en remercions.<br><br>";
        $content .="Malheureusement, nous ne pouvons donner suite à votre candidature.
                    Nous vous remercions cependant du temps que vous nous avez consacré et de l’intérêt que vous portez à notre entreprise";

        $this->sender->send(
            $candidateResume->getEmail(),
            $candidateResume->getNomcomplet(),
            $content,
            "Réponse à votre candidature chez CAPITAL RH"
        );

        $this->entityManager->flush ();

        $this->addFlash('notice',"Candidature mise à jour");
        return $this->redirect ($this->urlDispatcher (Action::INDEX));
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
