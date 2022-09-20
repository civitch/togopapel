<?php


namespace App\Controller\Admin;

use App\Entity\Annonce;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Services\Notification\AnnonceNotification;
use App\Services\Notification\ConversationNotification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ConversationController
 * @package App\Controller\Admin
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
#[Route(path: '/conversation')]
class ConversationController extends AbstractController
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
     * @param string $slug
     * @param Request $request
     * @param ConversationNotification $notification
     * @param ConversationRepository $conversationRepository
     * @return RedirectResponse|Response
     */
    #[Route(path: '/new/{slug}', name: 'new_conversation_admin', methods: ['GET', 'POST'])]
    public function new(string $slug, Request $request, ConversationNotification $notification, ConversationRepository $conversationRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository(Annonce::class)->findOneBy(['slug' => $slug]);
        /** @var Annonce $annonce */
        if(!$annonce)
        {
            return $this->redirectToRoute('home_project');
        }

        if($annonce->getUser() == $this->getUser())
        {
            return $this->redirectToRoute('annonce_info', ['slug' => $annonce->getSlug()]);
        }

        $result = count($conversationRepository->checkIfConversationExist($annonce->getUser(), $this->getUser(), $annonce)) >= 1;
        if($result){
            $this->addFlash('warning', 'Vous avez déjà une conversation pour cette annonce');
            return $this->redirectToRoute('annonce_info', ['slug' => $annonce->getSlug()]);
        }


        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            // Traitement de l'envoie de message
            $message
                ->setSender($this->getUser())
                ->setReceiver($annonce->getUser())
            ;

            $email=$annonce->getUser()->getEmail();
            $url="http://{$_SERVER['SERVER_NAME']}/conversation/liste";
            $this->notificationMessage($email,"Nouveau message",$url);
            // Conversation
            $conversation = new Conversation();
            $conversation
                ->setAnnonce($annonce)
                ->addMessage($message)
                ->setUsers([$annonce->getUser()->getId(), $this->getUser()->getId()])
            ;
            $em->persist($conversation);
            $em->persist($message);
            $em->flush();

            // Notfication l'annonceur
            $title_annonce = substr($annonce->getTitle(), 0, 7).'...';
            $notification->sendMessage(
                'Message reçu',
                "Envoyé par {$this->getUser()->getEmail()} pour l'annonce {$title_annonce}",
                $annonce->getUser()
            );


            return $this->redirectToRoute('liste_conversation_admin');
        }
        return $this->render('Admin/Conversation/new.html.twig', ['annonce' => $annonce, 'form' => $form->createView()]);
    }

    /**
     * Affiche la liste des conversations
     *
     * @param ConversationRepository $conversationRepository
     * @return Response
     */
    #[Route(path: '/liste', name: 'liste_conversation_admin', methods: ['GET'])]
    public function liste(ConversationRepository $conversationRepository): Response
    {
        return $this->render('Admin/Conversation/list.html.twig', [
            'conversations' => $conversationRepository->getConversationByUser($this->getUser())
        ]);
    }

    /**
     * Permet l'envoi de message
     */
    #[Route(path: '/tchat', name: 'message_tchat_admin', options: ['expose' => true], methods: ['POST'])]
    public function tchat(Request $request, ConversationNotification $notification)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $conservation = $em->getRepository(Conversation::class)->find($request->request->get('conversation'));
            if(!$conservation instanceof \App\Entity\Conversation)
            {
                return new JsonResponse(
                    ['error' => 'Aucune conversation de cet type'],
                    JsonResponse::HTTP_NOT_FOUND,
                    ['content-type' => 'application/json']
                );
            }
            /** @var Conversation $conversation */
            $users = $conservation->getUsers();
            $responseUser = null;
            // Récupérer l'utilisateur à qui l'on doit envoyer le message
            foreach ($users as $user)
            {
                if($user !== $this->getUser()->getId()){
                    $responseUser = $user;
                }
            }
            $receiver = $em->getRepository(User::class)->find($responseUser);
            $conservation->setUpdatedAt(new \DateTime());
            /** @var User $receiver */
            $message = new Message();
            $message
                ->setConversation($conservation)
                ->setContent($request->request->get('content'))
                ->setReceiver($receiver)
                ->setSender($this->getUser());
            $em->persist($message);
            $em->flush();
            $notification->sendMessage(
                'Message reçu',
                "Vous avez reçu un message de {$this->getUser()->getUsername()}",
                $receiver,
                true
            );
            $email=$receiver->getEmail();
            $url="http://{$_SERVER['SERVER_NAME']}/conversation/liste";
            $this->notificationMessage($email,"Nouveau message",$url);
            return new JsonResponse(
                ['success' => 'Message ajouté'],
                JsonResponse::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }
        return $this->redirectToRoute('home_project');
    }

    /**
     * Permet de récupérer la liste des messages pour une conversation
     *
     * @param Request $request
     * @param MessageRepository $messageRepository
     * @return JsonResponse|Response|RedirectResponse
     */
    #[Route(path: '/all', name: 'all_messages_users', options: ['expose' => true], methods: ['POST'])]
    public function allMessages(Request $request, MessageRepository $messageRepository)
    {
        if($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $conversation = $em->getRepository(Conversation::class)->find($request->request->get('conv'));
            if(!$conversation instanceof \App\Entity\Conversation)
            {
                return new JsonResponse(
                    ['error' => 'Aucune conversation de cet type'],
                    JsonResponse::HTTP_NOT_FOUND,
                    ['content-type' => 'application/json']
                );
            }
            /** @var Conversation $conversation */
            $messages = $messageRepository->getMessageByConversation($this->getUser(), $conversation);
            return $this->render('Admin/Conversation/all.html.twig', ['messages' => $messages]);
        }
        return $this->redirectToRoute('liste_conversation_admin');
    }

    /**
     * Mail de création de compte
     *
     * @param string $destEmail
     * @param int $id
     * @param string $token
     */
    public function notificationMessage(string $destEmail, string $objet,$url)
    {
        $options = [
            'url' => $url,
            "pathImage" => "https://{$_SERVER['SERVER_NAME']}/build/"
        ];
        $this->tplmail($destEmail, $objet, 'Email/notification-message.html.twig', $options);
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
            ->from(new Address($this->senderEmail, 'Togopapel'))
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
}
