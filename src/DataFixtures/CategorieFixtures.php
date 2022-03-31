<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Rubrique;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $rubique = $manager->getRepository(Rubrique::class)->find(8);
        $category = new Categorie();

        $category->setTitle('Accessoire beauté');
        $title = $category->getTitle();
        $slug = (new Slugify())->slugify($title);
        $category->setSlug($slug)->setRubrique($rubique);
        $manager->persist($category);
        $manager->flush();
    }
}
