<?php

namespace App\Controller\Admin;

use App\Controller\Admin\StatsController;


use App\Controller\Admin\StudentCrudController;
use App\Entity\Student;
use App\Entity\Book;
use App\Entity\Borrowing;
use App\Entity\Author;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(StudentCrudController::class)->generateUrl());
    }
    #[Route('/admin', name: 'admin')]
    public function index2(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(BorrowingCrudController::class)->generateUrl();
        return $this->redirect($url);
    }
    #[Route('/admin', name: 'admin')]
    public function index3(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(BookCrudController::class)->generateUrl();
        return $this->redirect($url);
    }
    #[Route('/admin', name: 'admin')]
    public function index4(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(AuthorCrudController::class)->generateUrl();
        return $this->redirect($url);
    }
    #[Route('/admin', name: 'admin')]
    public function index5(): Response
    {
        $routeBuilder = $this->container->get(AdminUrlGenerator::class);
        $url = $routeBuilder->setController(UserCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="assets/img/book.png" class="img-fluid d-block mxauto" style="max-width:100px; width:100%;"><h2 class="mt-3 fw-bold text-white text-center">Librarian</h2>')
            ->renderContentMaximized();

    }
    
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Students', 'fas fa-chalkboard-teacher', Student::class);
        yield MenuItem::linkToCrud('Borrowing', 'fas fa-book-reader', Borrowing::class);
        yield MenuItem::linkToCrud('Book', 'fas fa-book', Book::class);
        yield MenuItem::linkToCrud('Author', 'fas fa-pencil', Author::class);
        yield MenuItem::linkToCrud('User Panel', 'fas fa-user', User::class);
    }
}
