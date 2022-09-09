<?php


namespace App\Controller\Admin;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use App\Services\Notification\AnnonceNotification;
use Cocur\Slugify\Slugify;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Mime\Address;

/**
 * Class AnnonceController
 * @package App\Controller\Admin
 * @Route("/admin/annonce")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class AnnonceController extends AbstractController
{

    public function __construct(
        private MailerInterface $mailer, $senderEmail
    )
    {
        $this->senderEmail = $senderEmail;
    }
    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @Route("/ajout", name="announce_new", methods={"GET", "POST"})
     */
    public function new(Request $request)
    {
        $annonce = new Annonce();
        $user = $this->getUser();
        //dump($user);
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $slug = new Slugify();
            $em = $this->getDoctrine()->getManager();
            $user->setName($form->get('name')->getData());
            $user->setFirstname($form->get('firstname')->getData());
            $user->setTel($form->get('tel')->getData());
            $user->setIndicatifPays($form->get('indicatifPays')->getData());

            $annonce->setUser($this->getUser());

            $exitSlug=true;
            $cpt=0;
            do {
                $title=$annonce->getTitle();
                #$title="3Vision-Group , Agence SEO";
                $slugAnnonce=$slug->slugify($title);
                if($cpt!=0)
                {
                    $slugAnnonce=$slug->slugify($title.'-'.($cpt+1));
                }
                $annonceTest = $em->getRepository(Annonce::class)->findOneBy(['slug' =>$slugAnnonce]);
                if(!$annonceTest)
                {
                    $exitSlug=false;
                }
                $cpt++;
            }while($exitSlug==true);
            $email=$annonce->getUser()->getEmail();
            $url="http://{$_SERVER['SERVER_NAME']}/annonce/{$annonce->getSlug()}";
        $this->createAnnonceMail($email,"Nouvelle annonce ",$url);

            $annonce->setSlug($slugAnnonce);

            $em->persist($annonce);
            $em->flush();
            $this->addFlash("success", "Annonce crée avec succès, en attente de validation");
            return $this->redirectToRoute('announce_list_user');
        }

        #$email=$annonce->getUser()->getEmail();
        


        return $this->render('Admin/Annonce/new.html.twig', ['form' => $form->createView()]);
    }
    public function createAnnonceMail(string $destEmail, string $objet,$url)
    {
        $options = [
            'url' => $url,
            "pathImage" => "https://{$_SERVER['SERVER_NAME']}/build/img/"
        ];
        $this->tplmail($destEmail, $objet, 'Email/new-annonce.html.twig', $options);
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
     * @Route("/liste", name="announce_list_user", methods={"GET"})
     */
    public function liste(AnnonceRepository $annonceRepository)
    {
        $annonces = $annonceRepository->getAnnoncesByOwner($this->getUser());
        return $this->render('Admin/Annonce/list.html.twig', ['annonces' => $annonces]);
    }

    /**
     * @Route("/edit/{id}", name="annonce_edit", methods={"GET", "POST"}, requirements={"id" = "\d+"})
     */
    public function edit(Annonce $annonce, Request $request, AnnonceNotification $notification)
    {
        $this->denyAccessUnlessGranted('edit', $annonce);
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
            $user->setName($form->get('name')->getData());
            $user->setFirstname($form->get('firstname')->getData());
            $user->setTel($form->get('tel')->getData());
            $user->setIndicatifPays($form->get('indicatifPays')->getData());
            $em->flush();
            $notification->notifAdmins(
                'Modification d\'une annonce',
                "L'annonce {$annonce->getTitle()} a été modifiée par {$annonce->getUser()->getEmail()}"
            );
            $this->addFlash("info", "Annonce modifiée avec succès");
            return $this->redirectToRoute('announce_list_user');
        }
        return $this->render('Admin/Annonce/edit.html.twig', ['annonce' => $annonce, 'form' => $form->createView()]);
    }

    /**
     * @Route("/voir/{slug}", name="annonce_show", methods={"GET"})
     */
    public function show($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository(Annonce::class)->findOneBy(["slug" => $slug]);
        if(!$annonce)
        {
            return $this->redirectToRoute('announce_list_user');
        }
       // $this->denyAccessUnlessGranted('view', $annonce);
        return $this->render('Admin/Annonce/show.html.twig', ['annonce' => $annonce]);
    }

    /**
     * @Route("/delete", name="annonce_delete", methods={"DELETE"}, options={"expose"=true})
     */
    public function delete(Request $request)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $annonce = $em->getRepository(Annonce::class)->find($request->request->getInt('id'));
            if($annonce){
                $this->denyAccessUnlessGranted('delete', $annonce);
                if($this->isCsrfTokenValid('delete-annonce' . $annonce->getId(), $request->request->get('token')))
                {
                    $em->remove($annonce);
                    $em->flush();
                    return new JsonResponse(
                        ['success' => 'Supprimer avec success'],
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
