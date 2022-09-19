<?php

namespace App\Controller\Core;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParamController extends AbstractController
{
    #[Route('/core/param', name: 'app_core_param')]
    public function index(): Response
    {
        return $this->render('core/param/index.html.twig', [
            'controller_name' => 'ParamController',
        ]);
    }
}
