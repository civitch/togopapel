<?php


namespace App\Controller\Core;

use App\Entity\PostType;
use App\Form\PostEditType;
use App\Form\PostTypeFormType;
use App\Repository\PostTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class PostTypeController
 * @package App\Controller\Core
 * @Route("/corporate/postype")
 */
class PostTypeController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @return Response|RedirectResponse
     * @Route("/ajout", name="corporate_postype_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $postType = new PostType();
        $form = $this->createForm(PostTypeFormType::class, $postType);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($postType);
            $this->em->flush();
            $this->addFlash('success', 'PostType ajouté avec succès!');
            return $this->redirectToRoute('corporate_postype_list');
        }
        return $this->render('Core/PostType/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param PostType $postType
     * @return Response|RedirectResponse
     * @Route("/edit/{id}", name="corporate_postype_edit", requirements={"id"= "\d+"}, methods={"GET", "POST"})
     */
    public function edit(PostType $postType, Request $request): Response
    {
        $form = $this->createForm(PostEditType::class, $postType);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('info', 'PostType modifiée avec succès!');
            return $this->redirectToRoute('corporate_postype_list');
        }
        return $this->render('Core/PostType/edit.html.twig', ['form' => $form->createView()]);
    }


    /**
     * @Route("/liste", name="corporate_postype_list", methods={"GET"})
     */
    public function liste(PostTypeRepository $postTypeRepository): Response
    {
        return $this->render('Core/PostType/list.html.twig', ['postTypes' => $postTypeRepository->findAll()]);
    }


}
