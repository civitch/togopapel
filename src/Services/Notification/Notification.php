<?php


namespace App\Services\Notification;


use App\Entity\Notification as EntityNotification;
use App\Entity\NotificationUser;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class Notification
{
    /**
     * @var $em EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Notification à envoyer à tous les administrateurs du projet
     *
     * @param string $title
     * @param string $message
     * @param int $role
     */
    final protected function notifyAdmins(string $title, string $message, int $role)
    {
        $admins = $this->em->getRepository(User::class)->listeMainAdmins();
        $notification = new EntityNotification();
        $notification
            ->setTitle($title)
            ->setMessage($message)
            ->setRole($role)
        ;
        $this->em->persist($notification);

        // Envoyer ces infos à tous les admins
        foreach($admins as $admin)
        {
            $notificationUser = new NotificationUser();
            $notificationUser
                ->setUser($admin)
                ->setNotification($notification)
            ;
            $this->em->persist($notificationUser);
        }
        $this->em->flush();
    }

    /**
     * Permet d'envoyer une notification à un utilisateur
     *
     * @param string $title
     * @param string $message
     * @param UserInterface $owner
     * @param int $role
     * @param bool $flush
     */
    final protected function notfifyOwner(string $title, string $message, UserInterface $owner, int $role, $flush = false)
    {
        $notification = new EntityNotification();
        $notification
            ->setTitle($title)
            ->setMessage($message)
            ->setRole($role)
        ;

        $notificationUser = new NotificationUser();
        $notificationUser
            ->setUser($owner)
            ->setNotification($notification)
        ;

        $this->em->persist($notification);
        $this->em->persist($notificationUser);
        if(!$flush){
            $this->em->flush();
        }
    }

    /**
     * Notification crédit/débit wallet
     *
     * @param string $title
     * @param string $message
     * @param UserInterface $owner
     */
    public function wallet(string $title, string $message, UserInterface $owner)
    {
        $this->notfifyOwner($title, $message, $owner, EntityNotification::ROLES['wallet']);
    }
}
