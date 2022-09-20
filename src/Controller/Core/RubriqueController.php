<?php


namespace App\Controller\Core;


use App\Entity\Rubrique;
use App\Form\RubriqueType;
use App\Repository\RubriqueRepository;
use App\Services\Entity\RubriqueEntity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RubriqueController
 * @package App\Controller\Core
 */
#[Route(path: '/corporate')]
class RubriqueController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route(path: '/rubrique', name: 'rubrique_corporate', methods: ['POST', 'GET'])]
    public function index(Request $request, RubriqueEntity $rubriqueEntity, RubriqueRepository $rubriqueRepository)
    {
        $rubrique = new Rubrique();
        $form = $this->createForm(RubriqueType::class, $rubrique);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $rubriqueEntity->persistEntity($rubrique);
            return $this->redirectToRoute('rubrique_corporate');
        }
        return $this->render('Core/Rubrique/index.html.twig', $rubriqueEntity->options($form, $rubriqueRepository->findAll()));
    }
}
