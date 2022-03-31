<?php


namespace App\Services\Entity;


use App\Entity\Rubrique;
use Cocur\Slugify\Slugify;
use Symfony\Component\Form\FormInterface;

class RubriqueEntity extends Entity
{

    /**
     * Traitement de la persistence de l'objet Rubrique
     *
     * @param Rubrique $rubrique
     */
    public function persistEntity(Rubrique $rubrique)
    {
        $slug = new Slugify();
        $rubrique->setSlug($slug->slugify($rubrique->getTitle()));
        $this->em->persist($rubrique);
        $this->em->flush();
        $this->flashBag->add('success', 'Ajout rubrique avec succès');
    }


}
