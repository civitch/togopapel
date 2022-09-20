<?php

namespace App\Entity;

use App\Repository\CatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CatRepository::class)]
class Cat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\OneToMany(targetEntity: \App\Entity\Etiquette::class, mappedBy: 'cat')]
    private $etiquettes;

    public function __construct()
    {
        $this->etiquettes = new ArrayCollection();
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

    /**
     * @return Collection|Etiquette[]
     */
    public function getEtiquettes(): Collection
    {
        return $this->etiquettes;
    }

    public function addEtiquette(Etiquette $etiquette): self
    {
        if (!$this->etiquettes->contains($etiquette)) {
            $this->etiquettes[] = $etiquette;
            $etiquette->setCat($this);
        }

        return $this;
    }

    public function removeEtiquette(Etiquette $etiquette): self
    {
        if ($this->etiquettes->contains($etiquette)) {
            $this->etiquettes->removeElement($etiquette);
            // set the owning side to null (unless already changed)
            if ($etiquette->getCat() === $this) {
                $etiquette->setCat(null);
            }
        }

        return $this;
    }
}
