<?php


namespace App\Controller\Core;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 * @package App\Controller\Core
 */
#[Route(path: '/corporate')]
class DashboardController extends AbstractController
{
    #[Route(path: '/dashboard', name: 'dashboard_corporate', methods: ['GET'])]
    public function index()
    {
        return $this->render('Core/Dashboard/index.html.twig');
    }
}
