<?php

namespace App\Services\Notification;

use App\Entity\Notification as EntityNotification;
use App\Entity\User;

final class DemandeCreditNotification extends Notification{

    /**
     * Notification lors de l'ajout d'une demnde de crédit
     *
     * @param string $title
     * @param string $message
     */
    public function notifAdmins(string $title, string $message)
    {
        $this->notifyAdmins($title, $message, EntityNotification::ROLES['credit']);
    }

    /**
     * Notification lors de la validation d'une demande de crédit
     *
     * @param string $title
     * @param string $message
     * @param User $owner
     */
    public function validation(string $title, string $message, User $owner)
    {
        $this->notfifyOwner($title, $message, $owner, EntityNotification::ROLES['validation_credit']);
    }


}
