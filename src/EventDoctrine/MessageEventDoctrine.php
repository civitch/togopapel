<?php


namespace App\EventDoctrine;

use App\Entity\Message;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class MessageEventDoctrine
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Message) {
            return;
        }

        // TODO Envoyer un message au destinataire et un mail
        $entityManager = $args->getObjectManager();
    }
}
