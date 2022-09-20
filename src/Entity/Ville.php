<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
#[UniqueEntity(fields: ['title'], errorPath: 'title', message: 'Le titre de la ville doit être unique!')]
class Ville
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Assert\Length(max: 100, maxMessage: 'La ville ne doit pas dépasser {{ limit }} caractères!')]
    #[Assert\NotBlank(message: 'Le titre de la ville ne doit pas être vide!')]
    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private $title;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Region::class, inversedBy: 'villes')]
    #[ORM\JoinColumn(nullable: false)]
    private $region;

    #[ORM\OneToMany(targetEntity: \App\Entity\Annonce::class, mappedBy: 'ville')]
    private $annonces;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    public function __construct()
    {
        $this->annonces = new ArrayCollection();
    }

    public function getFirstRegion()
    {
        return substr(ucfirst($this->getRegion()->getTitle()), 0, 1);
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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return Collection|Annonce[]
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): self
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces[] = $annonce;
            $annonce->setVille($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonces->contains($annonce)) {
            $this->annonces->removeElement($annonce);
            // set the owning side to null (unless already changed)
            if ($annonce->getVille() === $this) {
                $annonce->setVille(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

}
