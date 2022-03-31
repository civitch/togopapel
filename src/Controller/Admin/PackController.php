<?php


namespace App\Controller\Admin;


use App\Entity\Pack;
use App\Entity\User;
use App\Entity\UserPack;
use App\Repository\PackRepository;
use App\Repository\UserPackRepository;
use App\Services\App\AppSecurity;
use App\Services\Entity\UserPackEntity;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PackController
 * @package App\Controller\Admin
 * @Route("/admin/pack")
 */
class PackController extends AbstractController
{
    /**
     * Affiche la liste des packs disponible
     *
     * @param PackRepository $packRepository
     * @return Response
     * @Route("/liste", name="pack_admin_liste", methods={"GET"})
     */
    public function index(PackRepository $packRepository)
    {
        return $this->render('Admin/Pack/index.html.twig',['packs' => $packRepository->findAll()]);
    }

    /**
     * Affiche un pack précis
     *
     * @Route("/{id}-{slug}", name="pack_admin_show", requirements={"slug": "[a-z0-9\-]*", "id" = "\d+"})
     * @param Pack $pack
     * @param $slug
     * @return Response
     */
    public function show(Pack $pack, $slug)
    {
        if ($pack->getSlug() !== $slug) {
            return $this->redirectToRoute('pack_admin_show', [
                'slug' => $pack->getSlug(),
                'id'   => $pack->getId()
            ], 301);
        }
        return $this->render('Admin/Pack/show.html.twig', compact('pack'));
    }

    /**
     * Ajouter un pack à la liste
     *
     * @Route("/add/{id}", name="pack_admin_add", methods={"POST"}, requirements={"id" = "\d+"})
     * @throws Exception
     */
    public function addPack(Pack $pack, Request $request, AppSecurity $appSecurity, UserPackEntity $userPackEntity)
    {
        $user = $this->getUser();
        /** @var User $user */
        if(empty($user->getWallet()) || ($user->getWallet() < $pack->getPrice())){
            $this->addFlash('warning', "Vos GDC ne sont pas suffisant pour acquérir ce pack");
            return $this->redirectToRoute('pack_admin_liste');
        }
        if(
            !$this->isCsrfTokenValid('add-pack-user', $request->request->get('token-pack')) ||
            $user->hasPack($pack) ||
            $user->hasRole($appSecurity->getRole('maintenance')) ||
            $user->hasRole($appSecurity->getRole('admin')) ||
            $user->hasRole($appSecurity->getRole('super_admin')) ||
            $user->hasRole($appSecurity->getRole('moderateur'))
        )
        {
            $this->addFlash('danger', 'Accès refusé pour l\'obtention de ce pack !');
            return $this->redirectToRoute('pack_admin_liste');
        }
        $userPackEntity->addUserPack($pack, $user);
        return $this->render('Admin/Pack/add.html.twig', compact('pack'));
    }

}
