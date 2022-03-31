<?php


namespace App\Services\Notification;

use App\Entity\Notification as EntityNotification;
use App\Entity\User;

final class PackNotification extends Notification
{
    /**
     * Notification lors de l'ajout d'un ajout de pack
     *
     * @param string $title
     * @param string $message
     */
    public function notifAdmins(string $title, string $message)
    {
        $this->notifyAdmins($title, $message, EntityNotification::ROLES['user_pack']);
    }

    /**
     * Notification lors de la suppression d'un pack
     *
     * @param string $title
     * @param string $message
     * @param User $owner
     */
    public function notifDeletePack(string $title, string $message, User $owner)
    {
        $this->notfifyOwner($title, $message, $owner, EntityNotification::ROLES['pack_expire'], true);
    }



}
