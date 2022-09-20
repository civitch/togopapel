<?php


namespace App\Controller\Admin;


use App\Entity\Annonce;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 * @package App\Controller\Admin
 */
#[Route(path: '/admin')]
class DashboardController extends AbstractController
{
    #[Route(path: '/tableau-de-bord', name: 'main_dashboard', methods: ['GET'])]
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $annonces = $em->getRepository(Annonce::class)->getAnnoncesByOwner($this->getUser(), true);
        return $this->render('Admin/Dashboard/index.html.twig', ['annonces' => $annonces]);
    }


}
