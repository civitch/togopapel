<?php


namespace App\Services\Entity;


use App\Entity\Pack;
use App\Entity\UserPack;
use App\Entity\User;

class UserPackEntity extends Entity
{
    const STAR = 1;
    const VIP = 2;
    const PREMIUM = 3;


    /**
     * Permet de supprimer le pack de l'annnonce
     *
     * @param UserPack $userPack
     */
    public function nullableAnnonce(UserPack $userPack)
    {
        $annonces = $userPack->getUser()->getAnnonces();
        if(!$annonces->isEmpty())
        {
            foreach ($annonces as $annonce){
                switch ($userPack->getPack()->getRole())
                {
                    case self::STAR:
                        $annonce->setPackStar(null);
                        break;
                    case self::VIP:
                        $annonce->setPackVip(null);
                        break;
                    case self::PREMIUM:
                        $annonce->setPackPremium(null);
                        break;
                }
            }
        }
    }

    /**
     * Fonction lancé afin de supprimé un pack utilisateur lors de sa date de fin
     *
     * @throws \Exception
     */
    public function checkExpire()
    {
        $userPacks = $this->em->getRepository(UserPack::class)->findAll();
        $today = new \DateTime();
        foreach ($userPacks as $userPack){
            /** @var UserPack $userPack */
            if($today > $userPack->getEndAt()) {
                $this->em->remove($userPack);
                $this->nullableAnnonce($userPack);
                $this->packNotification->notifDeletePack(
                    "Expiration de votre pack",
                    "Votre pack {$userPack->getPack()->getTitle()} a expiré!",
                    $userPack->getUser()
                );
            }
        }
        $this->em->flush();
    }

    /**
     * Permet d'ajouter un pack
     *
     * @param Pack $pack
     * @param User $user
     */
    public function addUserPack(Pack $pack, User $user)
    {
        $userPack = new UserPack();
        $u = "P{$pack->getDuration()}D";
        try {
            $userPack
                ->setUser($user)
                ->setPack($pack)
                ->setEndAt(
                    (new \DateTime())->add(new \DateInterval($u))
                );
        }
        catch (\Exception $e) {

        }
        // Soustrait la valeur actuelle du portefeuille
        $wallet = $user->getWallet() - $pack->getPrice();
        if($wallet < 0){
            $user->setWallet(0);
        }
        else{
            $user->setWallet($wallet);
        }
        $this->em->persist($userPack);
        $this->em->flush();
        $this->packNotification->notifAdmins(
            'Achat d\'un pack',
            "Obtention du pack {$userPack->getPack()->getTitle()} par {$userPack->getUser()->getEmail()}"
        );
        $this->packNotification->wallet('Votre portefeuille à été débité',
            "Votre portefeuille a été débité de {$pack->getPrice()} GDC!",
            $userPack->getUser())
        ;
    }
}
