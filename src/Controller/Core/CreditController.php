<?php


namespace App\Controller\Core;


use App\Entity\Credit;
use App\Entity\DemandeCredit;
use App\Form\CreditType;
use App\Repository\DemandeCreditRepository;
use App\Services\App\AppSecurity;
use App\Services\Notification\DemandeCreditNotification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CreditController
 * @package App\Controller\Core
 * @IsGranted("ROLE_ADMIN")
 */
#[Route(path: '/corporate/forfait')]
class CreditController extends AbstractController
{
    /**
     * Affiche la liste des forfaits définis dans le projet et aussi fait l'ajout
     */
    #[Route(path: '/liste', name: 'credit_liste_corporate', methods: ['GET', 'POST'])]
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $credits = $em->getRepository(Credit::class)->findAll();
        $credit = new Credit();
        $form = $this->createForm(CreditType::class, $credit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($credit);
            $em->flush();
            $this->addFlash('success', 'Ajout d\'un crédit réussie');
            return $this->redirectToRoute('credit_liste_corporate');
        }
        return $this->render('Core/Credit/index.html.twig', ['form' => $form->createView(), 'credits' => $credits]);
    }

    /**
     * Modifie le crédit
     */
    #[Route(path: '/edit/{id}', name: 'credit_edit_corporate', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Credit $credit, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CreditType::class, $credit);
         $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $this->addFlash('info', 'Modification d\'un crédit réussie');
            $em->flush();
            return $this->redirectToRoute('credit_liste_corporate');
        }
        return $this->render('Core/Credit/edit.html.twig', ['form' => $form->createView()]);
    }


    /**
     * Supprime un crédit de la liste
     *
     * @IsGranted("ROLE_MAINTENANCE")
     */
    #[Route(path: '/delete/{id}', name: 'credit_delete_corporate', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Credit $credit, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if(!$this->isCsrfTokenValid('delete-credit' . $credit->getId(), $request->request->get('token')))
        {
            $this->addFlash('warning', 'Erreur lors de la suppression!');
            return $this->redirectToRoute('credit_liste_corporate');
        }
        $this->addFlash('danger', 'Suppression d\'un crédit');
        $em->remove($credit);
        $em->flush();
        return $this->redirectToRoute('credit_liste_corporate');
    }

    /**
     * Affiche la liste des demandes de crédit en cours
     *
     * @param DemandeCreditRepository $demandeCreditRepository
     * @return Response
     */
    #[Route(path: '/demande', name: 'credit_demande_corporate', methods: ['GET', 'POST'])]
    public function listeDemande(DemandeCreditRepository $demandeCreditRepository)
    {
        $demandes = $demandeCreditRepository->findBy(['enabled' => DemandeCredit::STATUS['waiting']], ['createdAt' => 'DESC']);
        return $this->render('Core/Credit/liste.html.twig', ['demandes' => $demandes]);
    }

    /**
     * Affiche la liste des demandes de crédit activées
     *
     * @param DemandeCreditRepository $demandeCreditRepository
     * @return Response
     */
    #[Route(path: '/actives', name: 'credit_demande_enabled_corporate', methods: ['GET'])]
    public function listeDemandeEnabled(DemandeCreditRepository $demandeCreditRepository): Response
    {
        $demandes = $demandeCreditRepository->findBy(['enabled' => DemandeCredit::STATUS['enabled']], ['createdAt' => 'DESC']);
        return $this->render('Core/Credit/enabled.html.twig', ['demandes' => $demandes]);
    }


    /**
     * Affiche la liste des demandes de rejetées
     *
     * @param DemandeCreditRepository $demandeCreditRepository
     * @return Response
     */
    #[Route(path: '/refus', name: 'credit_demande_refuse_corporate', methods: ['GET'])]
    public function listeRefuseDemande(DemandeCreditRepository $demandeCreditRepository): Response
    {
        $demandes = $demandeCreditRepository->findBy(['enabled' => DemandeCredit::STATUS['disabled']], ['createdAt' => 'DESC']);
        return $this->render('Core/Credit/disabled.html.twig', ['demandes' => $demandes]);
    }

    /**
     * Active une demande de crédit
     *
     * @param Request $request
     * @return RedirectResponse
     */
    #[Route(path: '/enabled/{id}', name: 'credit_enabled_corporate', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function activateDemande(Request $request, DemandeCredit $demandeCredit, AppSecurity $appSecurity, DemandeCreditNotification $notification)
    {
        $em = $this->getDoctrine()->getManager();
        if(!$this->isCsrfTokenValid('enabled-credit', $request->request->get('enabled-token')))
        {
            $this->addFlash('danger', 'Erreur lors de l\'activation');
            return $this->redirectToRoute('credit_demande_corporate');
        }
        if(
            $demandeCredit->getUser()->hasRole($appSecurity->getRole('maintenance')) ||
            $demandeCredit->getUser()->hasRole($appSecurity->getRole('admin')) ||
            $demandeCredit->getUser()->hasRole($appSecurity->getRole('super_admin')) ||
            $demandeCredit->getUser()->hasRole($appSecurity->getRole('moderateur'))
        )
        {
            $this->addFlash('danger', 'Action Refusée!!!');
            return $this->redirectToRoute('credit_demande_corporate');
        }
        /** @var DemandeCredit $demande */
        $wallet = $demandeCredit->getCredit()->getGdc() + $demandeCredit->getUser()->getWallet();
        $demandeCredit
            ->setEnabled(DemandeCredit::STATUS['enabled'])
            ->getUser()
            ->setWallet($wallet)
        ;
        $em->flush();
        $notification->validation('Activation de votre forfait',
            "Le forfait de {$demandeCredit->getCredit()->getGdc()} GDC a été approuvé!",
            $demandeCredit->getUser()
        );
        $notification->wallet('Votre portefeuille a été crédité',
            "Votre portefeuille a été crédité de {$demandeCredit->getCredit()->getGdc()} GDC!",
            $demandeCredit->getUser()
        );
        $this->addFlash('success', 'Activation de GDC avec succès');
        return $this->redirectToRoute('credit_demande_enabled_corporate');
    }

    /**
     * refuse une demande de crédit
     *
     * @param Request $request
     * @return RedirectResponse
     */
    #[Route(path: '/refus/{id}', name: 'credit_refuse_corporate', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function refuseDemande(Request $request, DemandeCredit $demandeCredit, DemandeCreditNotification $notification)
    {
        $em = $this->getDoctrine()->getManager();
        if(!$this->isCsrfTokenValid('refuse-credit', $request->request->get('refuse-token')))
        {
            $this->addFlash('danger', 'Erreur lors de l\'activation');
            return $this->redirectToRoute('credit_demande_refuse_corporate');
        }
        /** @var DemandeCredit $demande */
        $demandeCredit->setEnabled(DemandeCredit::STATUS['disabled']);
        $em->flush();
        $notification->validation('Forfait désapprouvé',
            "Le forfait de {$demandeCredit->getCredit()->getGdc()} GDC a été désapprouvé!",
            $demandeCredit->getUser())
        ;
        $this->addFlash('danger', 'Refus de GDC avec succès');
        return $this->redirectToRoute('credit_demande_refuse_corporate');
    }
}
