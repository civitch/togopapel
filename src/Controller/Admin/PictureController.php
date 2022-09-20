<?php


namespace App\Controller\Admin;


use App\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class PictureController
 * @package App\Controller\Admin
 */
#[Route(path: '/admin')]
class PictureController extends AbstractController
{
    #[Route(path: '/delete', name: 'admin.picture.delete', methods: ['DELETE'])]
    public function delete(Request $request) {
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $picture = $em->getRepository(Picture::class)->find($data['_id']);
        if($picture !== null)
        {
            if ($this->isCsrfTokenValid('delete' . $picture->getId(), $data['_token'])) {
                $em->remove($picture);
                $em->flush();
                return new JsonResponse(['success' => 1]);
            }
            return new JsonResponse(['error' => 'Aucun picture de ce type'], JsonResponse::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['error' => 'Token invalide'], JsonResponse::HTTP_BAD_REQUEST);
    }
}
