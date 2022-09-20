<?php


namespace App\Controller\Admin;


use App\Entity\Credit;
use App\Entity\DemandeCredit;
use App\Form\DemandeCreditType;
use App\Repository\CreditRepository;
use App\Repository\DemandeCreditRepository;
use App\Services\App\AppSecurity;
use App\Services\Notification\DemandeCreditNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CreditController
 * @package App\Controller\Admin
 */
#[Route(path: '/admin/forfait')]
class CreditController extends AbstractController
{
    /**
     * Affiche la liste des crédits disponible
     *
     * @param CreditRepository $creditRepository
     * @return Response
     */
    #[Route(path: '/liste', name: 'credit_list_admin', methods: ['POST', 'GET'])]
    public function index(Request $request, CreditRepository $creditRepository, AppSecurity $appSecurity, DemandeCreditNotification $notification)
    {
        $user = $this->getUser();
        if(
            $user->hasRole($appSecurity->getRole('maintenance')) ||
            $user->hasRole($appSecurity->getRole('admin')) ||
            $user->hasRole($appSecurity->getRole('super_admin')) ||
            $user->hasRole($appSecurity->getRole('moderateur'))
        )
        {
            return $this->redirectToRoute('home_project');
        }
        $demandeCredit = new DemandeCredit();
        $form = $this->createForm(DemandeCreditType::class, $demandeCredit);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if($form->isSubmitted() && $form->isValid())
        {
            $credit = $em->getRepository(Credit::class)->find($form->get('card')->getData());
            if(!$credit instanceof \App\Entity\Credit){
                $this->addFlash('danger', 'Erreur lors de demande de crédit!');
                return $this->render('Admin/Credit/list.html.twig',
                    ['form' => $form->createView(), 'credits' => $creditRepository->findAll()]
                );
            }
            $demandeCredit->setUser($user);
            /** @var Credit $credit */
            $demandeCredit->setCredit($credit);
            $em->persist($demandeCredit);
            $em->flush();
            $notification->notifAdmins(
                'Demande de crédit',
                "Demande de crédit d'un montant {$credit->getMontant()} F CFA pour {$credit->getGdc()} GDC a été faîtes par {$user->getUsername()}"
            );
            return $this->redirectToRoute('credit_list_owner');
        }
        $options =  ['credits' => $creditRepository->findAll(), 'form' => $form->createView()];
        return $this->render('Admin/Credit/list.html.twig', $options);
    }

    /**
     * Affiche la liste des demandes de crédit effectuées par l'utilisateur courant
     *
     * @param DemandeCreditRepository $demandeCreditRepository
     * @return Response
     */
    #[Route(path: '/owner', name: 'credit_list_owner', methods: ['GET'])]
    public function liste(DemandeCreditRepository $demandeCreditRepository)
    {
        return $this->render('Admin/DemandeCredit/list.html.twig', [
            'demandes' => $demandeCreditRepository->findBy(
            ['user' => $this->getUser()], ['createdAt' => 'DESC'])]
        );
    }
}
