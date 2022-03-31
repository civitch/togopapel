<?php


namespace App\Controller\Core;


use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use App\Services\Notification\AnnonceNotification;
use Cocur\Slugify\Slugify;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class AnnonceController
 * @package App\Controller\Core
 * @Route("/corporate/annonce")
 */
class AnnonceController extends AbstractController
{
    private $notification;
    private $mailer;
    private $senderEmail;

    public function __construct(AnnonceNotification $annonceNotification,MailerInterface $mailer, $senderEmail)
    {
        $this->notification = $annonceNotification;
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
    }


    /**
     * Lister Toutes les annonces des utilisateurs
     * @param AnnonceRepository $annonceRepository
     * @return Response
     * @Route("/liste", name="annonce_corp_liste", methods={"GET"})
     */
    public function liste(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findAll();
        return $this->render('Core/Annonce/list.html.twig', ['annonces' => $annonces]);
    }
    /**
     * Lister Toutes les annonces des utilisateurs
     * @param AnnonceRepository $annonceRepository
     * @return Response
     * @Route("/phpinfo", name="phpinfo", methods={"GET"})
     */
    public function phpinfo(AnnonceRepository $annonceRepository): Response
    {
        phpinfo();

    }

    /**
     * @param AnnonceRepository $annonceRepository
     * @return Response
     * @Route("/attente", name="annonce_corp_waiting", methods={"GET"})
     */
    public function listeWaiting(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findBy(['enabled' => Annonce::STATUS['waiting']], ['createdAt' => 'DESC']);
        return $this->render('Core/Annonce/waiting.html.twig', ['annonces' => $annonces]);
    }

    /**
     * @param AnnonceRepository $annonceRepository
     * @return Response
     * @Route("/active", name="annonce_corp_active", methods={"GET", "POST"}, options={"expose"=true})
     */
    public function listeEnabled(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findBy(['enabled' => Annonce::STATUS['enabled']], ['createdAt' => 'DESC']);
        return $this->render('Core/Annonce/enabled.html.twig', ['annonces' => $annonces]);
    }

    /**
     * @param AnnonceRepository $annonceRepository
     * @return Response
     * @Route("/desactive", name="annonce_corp_desactive", methods={"GET"})
     */
    public function listeDesactivate(AnnonceRepository $annonceRepository): Response
    {
        $annonces = $annonceRepository->findBy(['enabled' => Annonce::STATUS['disabled']], ['createdAt' => 'DESC']);
        return $this->render('Core/Annonce/disabled.html.twig', ['annonces' => $annonces]);
    }


    /**
     * @param Annonce $annonce
     * @return Response
     * @Route("/voir/{id}", name="annonce_corp_show", methods={"GET"}, requirements={"id" = "\d+"})
     */
    public function show(Annonce $annonce): Response
    {
        return $this->render('Core/Annonce/show.html.twig', ['annonce' => $annonce]);
    }


    /**
     * Permet de d'approuver une annonce
     *
     * @Route("/enabled", name="annonce_corporate_enabled", options={"expose" = true}, methods={"POST"})
     */
    public function enabledAnnonce(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $annonce = $em->getRepository(Annonce::class)->find($request->request->get('id'));
            if($annonce){
                $this->denyAccessUnlessGranted('edit', $annonce);
                if($this->isCsrfTokenValid('enabled-annonce' . $annonce->getId(), $request->request->get('token')))
                {
                    $annonce->setEnabled(Annonce::STATUS['enabled']);
                    $em->flush();
                    $title_annonce = substr($annonce->getTitle(), 0, 7).'...';
                    // Send Notification
                    $this->notification->validation(
                        'Validation de votre annonce',
                        "L'annonce {$title_annonce} a été activée avec succès!",
                        $annonce->getUser());

                    $email=$annonce->getUser()->getEmail();
                    #$email="yavouckolye@gmail.com";
                    $url="http://{$_SERVER['SERVER_NAME']}/annonce/{$annonce->getSlug()}";
                    $this->activateAnnonceMail($email,"Annonce activé",$url);


                    return new JsonResponse(
                        ['success' => 'Annonce activée '],
                        JsonResponse::HTTP_OK,
                        ['content-type' => 'application/json']
                    );
                }

                return new JsonResponse(
                    ['error' => 'token invalide'],
                    JsonResponse::HTTP_BAD_REQUEST,
                    ['content-type' => 'application/json']
                );
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
     * Mail de création de compte
     *
     * @param string $destEmail
     * @param int $id
     * @param string $token
     */
    public function activateAnnonceMail(string $destEmail, string $objet,$url)
    {
        $options = [
            'url' => $url,
            "pathImage" => "https://{$_SERVER['SERVER_NAME']}/build/img/"
        ];
        $this->tplmail($destEmail, $objet, 'Email/activate-annonce.html.twig', $options);
    }
    /**
     * Template de mail par défaut
     *
     * @param string $destEmail
     * @param string $subject
     * @param string $path
     * @param array $options
     */
    private function tplmail(string $destEmail, string $subject, string $path, $options = [])
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->senderEmail, 'togopapel'))
            ->to($destEmail)
            ->subject($subject)
            ->htmlTemplate($path)
            ->context($options)
        ;
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            echo $e->getMessage();
        }
    }
    /**
     * Permet de désapprouver une annonce
     *
     * @Route("/disabled", name="annonce_corporate_disabled", options={"expose" = true}, methods={"POST"})
     */
    public function disabledAnnonce(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $annonce = $em->getRepository(Annonce::class)->find($request->request->get('id'));
            if($annonce){
                $this->denyAccessUnlessGranted('edit', $annonce);
                if($this->isCsrfTokenValid('disabled-annonce' . $annonce->getId(), $request->request->get('token')))
                {
                    /** @var Annonce $annonce */
                    $annonce->setEnabled(Annonce::STATUS['disabled']);
                    $em->flush();
                    $title_annonce = substr($annonce->getTitle(), 0, 7).'...';
                    $this->notification->validation(
                        'Désapprobation de votre annonce',
                        "L'annonce {$title_annonce} a été désapprouvée!",
                        $annonce->getUser());
                    return new JsonResponse(
                        ['success' => 'Annonce désactivée'],
                        JsonResponse::HTTP_OK,
                        ['content-type' => 'application/json']
                    );
                }
                return new JsonResponse(
                    ['error' => 'token invalide'],
                    JsonResponse::HTTP_BAD_REQUEST,
                    ['content-type' => 'application/json']
                );
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
