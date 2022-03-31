<?php


namespace App\EventDoctrine;


use App\Entity\Annonce;
use App\Services\Notification\AnnonceNotification;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class AnnonceEventDoctrine implements EventSubscriber
{
    private $annonceNotification;

    public function __construct(AnnonceNotification $annonceNotification)
    {
        $this->annonceNotification = $annonceNotification;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->checkTypeAnnonce('persist', $args);
        $this->addNotification('persist', $args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->checkTypeAnnonce('update', $args);
    }

    /**
     * verifie si le type de l'annonce est une demande dans ce cas le prix sera nul
     *
     * @param string $action
     * @param LifecycleEventArgs $args
     */
    private function checkTypeAnnonce(string $action, LifecycleEventArgs $args)
    {
        $annonce = $args->getObject();
        if(!$annonce instanceof Annonce)
        {
            return;
        }
        if(!$annonce->getType())
        {
            $annonce->setPrice(null);
        }
        $args->getObjectManager()->flush();
    }

    /**
     * Evènement de notification lors de l'ajout d'une annonce
     *
     * @param string $action
     * @param LifecycleEventArgs $args
     */
    private function addNotification(string $action, LifecycleEventArgs $args)
    {
        $annonce = $args->getObject();
        if(!$annonce instanceof Annonce)
        {
            return;
        }
        $this->annonceNotification->notifAdmins(
            'Ajout d\'une annonce',
            "L'annonce {$annonce->getTitle()} a été ajoutée par {$annonce->getUser()->getEmail()}"
        );
    }




}
