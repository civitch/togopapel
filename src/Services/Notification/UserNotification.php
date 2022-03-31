<?php


namespace App\Services\Notification;

use App\Entity\Notification as EntityNotification;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserNotification extends Notification
{
    /**
     * @param string $title
     * @param string $message
     * @param bool $valid
     */
    public function accountCreated(string $title, string $message, $valid = false)
    {
        if($valid)
        {
            $this->notifyAdmins($title, $message, EntityNotification::ROLES['user_valid_account']);
        }else{
            $this->notifyAdmins($title, $message, EntityNotification::ROLES['user_create_account']);
        }
    }

    /**
     * @param string $title
     * @param string $message
     * @param UserInterface $owner
     */
    public function accountEdit(string $title, string $message, UserInterface $owner)
    {
        $this->notfifyOwner($title, $message, $owner, EntityNotification::ROLES['user_edit_account']);
    }


}
