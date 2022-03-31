<?php


namespace App\Services\Entity;


use App\Entity\Categorie;
use Cocur\Slugify\Slugify;

class CategorieEntity extends Entity
{
    /**
     * Traitement de la persistence de l'objet Catégorie
     *
     * @param Categorie $categorie
     */
    public function persistEntity(Categorie $categorie)
    {
        $title = $categorie->getTitle();
        $slug = (new Slugify())->slugify($title); 
        $categorie->setSlug($slug);
        $this->em->persist($categorie);
        $this->em->flush();
        $this->flashBag->add('success', 'Ajout catégorie avec succès');
    }
}
