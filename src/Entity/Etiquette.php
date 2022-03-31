<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EtiquetteRepository")
 */
class Etiquette
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PostType", mappedBy="etiquette")
     */
    private $postypes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cat", inversedBy="etiquettes")
     * @ORM\JoinColumn(nullable=true)
     */
    private $cat;

    public function __construct()
    {
        $this->postypes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPostTypes(): Collection
    {
        return $this->postypes;
    }

    public function addPostType(PostType $postType): self
    {
        if (!$this->postypes->contains($postType)) {
            $this->postypes[] = $postType;
            $postType->setEtiquette($this);
        }

        return $this;
    }

    public function getCat(): ?Cat
    {
        return $this->cat;
    }

    public function setCat(?Cat $cat): ?self
    {
        $this->cat = $cat;

        return $this;
    }
}
