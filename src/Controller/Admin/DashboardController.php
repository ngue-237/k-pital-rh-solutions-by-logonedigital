<?php

namespace App\Controller\Admin;

use App\Entity\Job;
use App\Entity\Adresse;
use App\Entity\CategoryJob;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;


class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('K-PITAL RH SOLUTIONS');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),
            //MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class),
            MenuItem::section  ('',''),

            // MenuItem::subMenu('Blog', 'fas fa-list')->setSubItems([
            //     MenuItem::linkToCrud('Thématiques blog', 'fa fa-tags', CategoryJob::class),
            //     MenuItem::linkToCrud('Posts', 'fa fa-file-text', Post::class),
            // ]),
            MenuItem::subMenu("Emplois", 'fas fa-list')->setSubItems([
                MenuItem::linkToCrud("Secteur d'activité", 'fa fa-tags', CategoryJob::class),
                MenuItem::linkToCrud("Offres d'emplois", 'fas fa-user-md', Job::class),
                MenuItem::linkToCrud ('Localisation','fas fa-map-marker-alt', Adresse::class)
            ]),
            MenuItem::section  ('',''),
            // MenuItem::linkToCrud('Courriel', 'fas fa-envelope', Contact::class)
            // ...
        ];
    }
}
