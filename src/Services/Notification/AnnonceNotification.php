<?php


namespace App\Services\Notification;

use App\Entity\Notification as EntityNotification;
use App\Entity\NotificationUser;
use App\Entity\User;

final class AnnonceNotification extends Notification{

    /**
     * Notification lors de l'ajout d'une annonce
     *
     * @param string $title
     * @param string $message
     */
    public function notifAdmins(string $title, string $message)
    {
        $this->notifyAdmins($title, $message, EntityNotification::ROLES['annonce']);
    }

    /**
     * Notification validation d'une annonce
     *
     * @param string $title
     * @param string $message
     * @param User $owner
     */
    public function validation(string $title, string $message, User $owner)
    {
        $this->notfifyOwner($title, $message, $owner, EntityNotification::ROLES['validation_annonce']);
    }
}
