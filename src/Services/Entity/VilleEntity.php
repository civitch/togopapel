<?php


namespace App\Services\Entity;


use App\Entity\Ville;
use Cocur\Slugify\Slugify;
use Symfony\Component\Form\FormInterface;

class VilleEntity extends Entity
{
    /**
     * Traitement de la persistence de l'objet Région
     *
     * @param Ville $ville
     */
    public function persistEntity(Ville $ville)
    {
        $slug = new Slugify();
        $ville->setSlug($slug->slugify($ville->getTitle()));
        $this->em->persist($ville);
        $this->em->flush();
        $this->flashBag->add('success', 'Ajout ville avec succès');
    }

}
