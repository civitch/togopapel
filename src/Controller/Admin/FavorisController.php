<?php


namespace App\Controller\Admin;


use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FavorisController
 * @package App\Controller\Admin
 * @Route("/favoris")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class FavorisController extends AbstractController
{

    /**
     * @Route("/liste", name="favoris_owner_liste", methods={"POST", "GET"})
     */
    public function index(AnnonceRepository $annonceRepository)
    {
        $favoris = $annonceRepository->getFavorisByUser($this->getUser());
        return $this->render('Admin/Favoris/index.html.twig', compact('favoris'));
    }

    /**
     * @Route("/new", name="favoris_new", methods={"POST"}, options={"expose"=true})
     */
    public function add(Request $request)
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('liste_annonce_front');
        }
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $annonce = $em->getRepository(Annonce::class)->find($request->request->getInt('id'));
            /** @var $annonce Annonce */
            if($annonce) {
                $user->addFavori($annonce);
                $em->flush();
                return new JsonResponse(['annonce' => $annonce->getId()], 200, ['content-type' => 'application/json']);
            }
            return new JsonResponse(
                ['error' => 'Aucune annonce de cet type'],
                JsonResponse::HTTP_NOT_FOUND,
                ['content-type' => 'application/json']
            );
        }
        return $this->redirectToRoute('home_project');
    }

    /**
     * @Route("/delete", name="favoris_delete", methods={"POST"}, options={"expose"=true})
     */
    public function delete(Request $request)
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('liste_annonce_front');
        }
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $annonce = $em->getRepository(Annonce::class)->find($request->request->getInt('id'));
            /** @var $annonce Annonce */
            if($annonce) {
                $user->removeFavori($annonce);
                $em->flush();
                return new JsonResponse(['annonce' => $annonce->getId()], 200, ['content-type' => 'application/json']);

            }
            return new JsonResponse(
                ['error' => 'Aucune annonce de cet type'],
                JsonResponse::HTTP_NOT_FOUND,
                ['content-type' => 'application/json']
            );
        }
        return $this->redirectToRoute('home_project');
    }


}
