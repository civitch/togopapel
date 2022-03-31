<?php


namespace App\Controller\Core;


use App\Entity\Option;
use App\Form\Option\HomeOptionType;
use App\Form\Option\OptionType;
use App\Repository\OptionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OptionController
 * @package App\Controller\Core
 * @Route("/corporate")
 */
class OptionController extends AbstractController
{
    /**
     * @Route("/option/new", name="corporate_option_new", methods={"GET", "POST"})
     * @param Request $request
     * @return Response|RedirectResponse
     * @IsGranted("ROLE_MAINTENANCE")
     */
    public function new(Request $request): Response
    {
        $option = new Option();
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($option);
            $em->flush();
            $this->addFlash('success', 'Ajout d\'option effectué avec succès');
            return $this->redirectToRoute('corporate_option_list');
        }
        return $this->render('Core/Option/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param OptionRepository $optionRepository
     * @return Response
     * @Route("/option/liste", name="corporate_option_list", methods={"GET"})
     * @IsGranted("ROLE_MAINTENANCE")
     */
    public function liste(OptionRepository $optionRepository): Response
    {
        return $this->render('Core/Option/list.html.twig', ['options' => $optionRepository->findAll()]);

    }


    /**
     * @param Option $option
     * @param Request $request
     * @Route("/option/home/edit/{id}", name="corporate_option_home_edit", methods={"GET", "POST"}, requirements={"id" = "\d+"})
     * @return RedirectResponse|Response
     */
    public function editHome(Option $option, Request $request): Response
    {
        $form = $this->createForm(HomeOptionType::class, $option);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('edit', 'Modification d\'otpion effectué avec succès');
            return $this->redirectToRoute('corporate_option_list');
        }
        return $this->render('Core/Option/edit.html.twig', ['option' => $option, 'form' => $form->createView()]);
    }

}