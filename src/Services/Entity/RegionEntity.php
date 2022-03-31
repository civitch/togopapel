<?php


namespace App\Services\Entity;

use App\Entity\Region;
use App\Form\RegionType;
use Cocur\Slugify\Slugify;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class RegionEntity extends Entity
{
    /**
     * Traitement de la persistence de l'objet Région
     *
     * @param Region $region
     */
    public function persistEntity(Region $region)
    {
        $slug = new Slugify();
        $region->setSlug($slug->slugify($region->getTitle()));
        $this->em->persist($region);
        $this->em->flush();
        $this->flashBag->add('success', 'Ajout région avec succès');
    }


}
