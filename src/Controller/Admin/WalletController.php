<?php


namespace App\Controller\Admin;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WalletController
 * @package App\Controller\Admin
 */
#[Route(path: '/admin')]
class WalletController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route(path: '/portefeuille', name: 'admin_wallet_user', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        /** @var User $user */
        $wallet = $user->getWallet();
        return $this->render('Admin/Wallet/index.html.twig', ['packs' => $user->getUserPacks(), 'wallet' => $wallet]);
    }

    
}
