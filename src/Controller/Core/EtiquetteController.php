<?php


namespace App\Controller\Core;


use App\Entity\Etiquette;
use App\Form\EtiquetteType;
use App\Repository\EtiquetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EtiquetteController
 * @package App\Controller\Core
 */
#[Route(path: '/corporate/etiquette')]
class EtiquetteController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route(path: '/ajout', name: 'corporate_etiquette_new', methods: ['GET', 'POST'])]
    public function new(Request $request)
    {
        $etiquette = new Etiquette();
        $form = $this->createForm(EtiquetteType::class, $etiquette);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($etiquette);
            $this->em->flush();
            $this->addFlash('success', 'Etiquette ajoutée avec succès!');
            return $this->redirectToRoute('corporate_etiquette_liste');
        }
        return $this->render('Core/Etiquette/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Etiquette $etiquette
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route(path: '/edit/{id}', name: 'corporate_etiquette_edit', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
    public function edit(Etiquette $etiquette, Request $request)
    {
        $form = $this->createForm(EtiquetteType::class, $etiquette);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Etiquette modifiée avec succès!');
            return $this->redirectToRoute('corporate_etiquette_liste');
        }
        return $this->render('Core/Etiquette/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param EtiquetteRepository $etiquetteRepository
     * @return Response
     */
    #[Route(path: '/liste', name: 'corporate_etiquette_liste', methods: ['GET'])]
    public function liste(EtiquetteRepository $etiquetteRepository): Response
    {
        return $this->render('Core/Etiquette/list.html.twig', ['etiquettes' => $etiquetteRepository->findAll()]);
    }
}

