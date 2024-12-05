<?php

namespace App\Controller\Admin;

use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin')]
class DashboardController extends AbstractController
{
    #[Route(path: '/dashboard', name: 'admin_dashboard_show')]
    public function dashboard(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('admin/pages/dashboard.html.twig');
    }
}
