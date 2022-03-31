<?php


namespace App\Services\Notification;

use App\Entity\Notification as EntityNotification;
use App\Entity\User;

final class ConversationNotification extends Notification
{
    /**
     * @param string $title
     * @param string $message
     * @param User $owner
     * @param bool $exchange
     */
    public function sendMessage(string $title, string $message, User $owner, $exchange = false)
    {
        if(!$exchange){
            $this->notfifyOwner($title, $message, $owner, EntityNotification::ROLES['send_message']);
        }
        else{
            $this->notfifyOwner($title, $message, $owner, EntityNotification::ROLES['message_exchange']);
        }
    }
}
