<?php


namespace App\Controller\Core;


use App\Entity\Pack;
use App\Entity\UserPack;
use App\Form\PackType;
use App\Repository\PackRepository;
use App\Repository\UserPackRepository;
use App\Services\Entity\UserPackEntity;
use App\Services\Notification\PackNotification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PackController
 * @package App\Controller\Core
 */
#[Route(path: '/corporate/pack')]
class PackController extends AbstractController
{
    /**
     * Ajoute un pack
     *
     * @param Request $request
     * @return RedirectResponse|Response
     * @IsGranted("ROLE_MAINTENANCE")
     */
    #[Route(path: '/ajout', name: 'pack_corporate_new', methods: ['GET', 'POST'])]
    public function new(Request $request)
    {
        $pack = new Pack();
        $form = $this->createForm(PackType::class, $pack);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pack);
            $em->flush();
            $this->addFlash('success', 'Pack Ajouté avec succès!');
            return $this->redirectToRoute('pack_list_corporate');
        }
        return $this->render('Core/Pack/index.html.twig', ['form' => $form->createView()]);
    }


    /**
     * Modifier un pack
     *
     * @param Pack $pack
     * @param Request $request
     * @return RedirectResponse|Response
     * @IsGranted("ROLE_MAINTENANCE")
     */
    #[Route(path: '/edit/{id}', name: 'pack_corporate_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Pack $pack, Request $request)
    {
        $form = $this->createForm(PackType::class, $pack);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('pack_list_corporate');
        }
        return $this->render('Core/Pack/index.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Liste les packs disponible
     *
     * @param PackRepository $packRepository
     * @return Response
     * @IsGranted("ROLE_MAINTENANCE")
     */
    #[Route(path: '/liste', name: 'pack_list_corporate', methods: ['GET'])]
    public function liste(PackRepository $packRepository): Response
    {
        return $this->render('Core/Pack/list.html.twig', ['packs' => $packRepository->findAll()]);
    }


    /**
     * Liste les utilisateurs ayant des packs
     *
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route(path: '/users', name: 'pack_users_corporate')]
    public function getPacks(UserPackRepository $userPackRepository)
    {
        return $this->render('Core/Pack/users.html.twig', ['userPacks' => $userPackRepository->findAll()]);
    }


    /**
     * Supprime la liaison d'un pack avec un utilisateur
     *
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route(path: '/delete/{id}', name: 'pack_delete_corporate', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function delete(UserPack $userPack, UserPackEntity $userPackEntity, PackNotification $packNotification)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($userPack);
        $userPackEntity->nullableAnnonce($userPack);
        $packNotification->notifDeletePack(
            "Suppression de votre pack par l'équipe",
            "L'équipe GDO a supprimé votre pack {$userPack->getPack()->getTitle()}",
            $userPack->getUser()
        );
        $em->flush();
        return $this->redirectToRoute('pack_users_corporate');
    }







}
