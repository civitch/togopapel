<?php


namespace App\Controller\Core;


use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Services\Entity\CategorieEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class CategorieController
 * @package App\Controller\Core
 * @Route("/corporate")
 */
class CategorieController extends AbstractController
{
    /**
     * @Route("/categorie", name="categorie_corporate", methods={"GET", "POST"})
     */
    public function index(Request $request, CategorieEntity $categorieEntity, CategorieRepository $categorieRepository)
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $categorieEntity->persistEntity($categorie);
            return $this->redirectToRoute('categorie_corporate');
        }
        return $this->render('Core/Categorie/index.html.twig', $categorieEntity->options($form, $categorieRepository->findAll()));
    }
}
