<?php


namespace App\Controller\Core;


use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\Notification\PackNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WalletController
 * @package App\Controller\Core
 * @Route("/corporate/wallet")
 */
class WalletController extends AbstractController
{
    /**
     * @Route("/users", name="users_wallet_corporate", methods={"GET", "POST"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('Core/Wallet/index.html.twig', ['users' => $userRepository->getWalletNotNull()]);
    }


    /**
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     * @Route("/disable/{id}", name="disable_wallet_corporate", methods={"POST"}, requirements={"id" = "\d+"})
     */
    public function disable(Request $request, User $user, PackNotification $packNotification): RedirectResponse
    {
        if(!$this->isCsrfTokenValid('disable-wallet'.$user->getId(), $request->request->get('token-wallet-disable')))
        {
            $this->addFlash('danger', 'Erreur lors de la désactivation wallet');
            return $this->redirectToRoute('users_wallet_corporate');
        }
        $em = $this->getDoctrine()->getManager();
        $user->setWallet(null);
        $packNotification->notifDeletePack(
            'Portefeuille vide',
            'L\'équipe GDO a supprimé vos GDC de votre portefeuille',
            $user
        );
        $em->flush();
        return $this->redirectToRoute('users_wallet_corporate');
    }
}
