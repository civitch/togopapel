<?php


namespace App\Controller\Core;


use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use App\Services\Entity\VilleEntity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class VilleController
 * @package App\Controller\Core
 */
#[Route(path: '/corporate')]
class VilleController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route(path: '/ville', name: 'ville_corporate', methods: ['GET', 'POST'])]
    public function index(Request $request, VilleEntity $villeEntity, VilleRepository $villeRepository)
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $villeEntity->persistEntity($ville);
            return $this->redirectToRoute('ville_corporate');
        }
        return $this->render('Core/Ville/index.html.twig', $villeEntity->options($form, $villeRepository->findAll()));
    }



}
