<?php


namespace App\Controller\Front;


use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route(path: '/profile/user/{slug}', name: 'profile_gdo_user', methods: ['GET'])]
    public function index(string $slug, AnnonceRepository $annonceRepository, Request $request)
    {
        $annonce = $annonceRepository->findOneBy(['slug' => $slug]);
        if(!$annonce instanceof \App\Entity\Annonce){
            return $this->redirectToRoute('home_project');
        }
        $options = [
            'annonce'  => $annonce,
            'annonces' => $annonceRepository->getAnnonceEnabledByUserPaginate(
                $annonce->getUser(),
                $request->query->getInt('page', 1),
                10
            )
        ];
        return $this->render('Front/Profile/index.html.twig', $options);
    }
}