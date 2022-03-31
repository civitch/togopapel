<?php


namespace App\Controller\Admin;


use App\Entity\Notification;
use App\Entity\NotificationUser;
use App\Repository\NotificationUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    const MAIN_PATH = 'Admin/Notification/';
    const LIMIT_PAGES = 10;
    const PARAMETER_FILTER = 1;
    const PARAMETER_VALUE = 'page';

    private $em;
    private $entityNotification;

    /**
     * NotificationController constructor.
     * @param EntityManagerInterface $em
     * @param NotificationUserRepository $notificationUserRepository
     */
    public function __construct(EntityManagerInterface $em, NotificationUserRepository $notificationUserRepository)
    {
        $this->em = $em;
        $this->entityNotification = $notificationUserRepository;
    }

    /**
     * @return Response
     */
    public function liste(): Response
    {
        $notifications = $this->entityNotification->getNotificationByUSer($this->getUser());
        return $this->render(self::MAIN_PATH."list.html.twig", ['notifications' => $notifications]);
    }

    /**
     * @return Response
     */
    public function listeMobile(): Response
    {
        $notifications = $this->entityNotification->getNotificationByUSer($this->getUser());
        return $this->render(self::MAIN_PATH."listMobile.html.twig", ['notifications' => $notifications]);
    }


    /**
     * @return Response
     */
    public function count(): Response
    {
        $notifications = count($this->entityNotification->getCountNotification($this->getUser()));
        return $this->render(self::MAIN_PATH."count.html.twig", ['notifications' => $notifications]);
    }

    /**
     * Afficher une notification par information
     *
     * @param NotificationUser $notificationUser
     * @return RedirectResponse
     * @throws \Exception
     * @Route("/admin/notification/show/{id}", name="notification_user_show", methods={"GET"}, requirements={"id" = "\d+"})
     */
    public function show(NotificationUser $notificationUser): RedirectResponse
    {
        if(is_null($notificationUser->getReadAt()))
        {
            $notificationUser->setReadAt(new \DateTime());
            $this->em->flush();
        }
        switch ($notificationUser->getNotification()->getRole()){
            case Notification::ROLES['validation_annonce']:
                return $this->redirectToRoute('announce_list_user');
                break;
            case Notification::ROLES['validation_credit']:
                return $this->redirectToRoute('credit_list_owner');
                break;
            case Notification::ROLES['wallet']:
            case Notification::ROLES['pack_expire']:
                return $this->redirectToRoute('admin_wallet_user');
                break;
            case Notification::ROLES['send_message']:
            case Notification::ROLES['message_exchange']:
                return $this->redirectToRoute('liste_conversation_admin');
                break;
            case Notification::ROLES['user_edit_account']:
                return $this->redirectToRoute('account_profile');
                break;
        }
        return $this->redirectToRoute('home_project');
    }


    /**
     * Liste de toutes les notifications par utilisateur
     *
     * @Route("/admin/notification/liste", name="notification_all_liste", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function allNotifications(Request $request): Response
    {
        $options = [
            'info'     => $_GET,
            'notifications' => $this->entityNotification->getAll(
                $this->getUser(),
                $request->query->getInt(self::PARAMETER_VALUE, self::PARAMETER_FILTER),
                self::LIMIT_PAGES
            )
        ];
        return $this->render(self::MAIN_PATH."all.html.twig", $options);
    }

}
