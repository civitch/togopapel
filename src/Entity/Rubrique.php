<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\RubriqueRepository")
 * @UniqueEntity(
 *     fields={"title"},
 *     errorPath="title",
 *     message="Le titre de la rubrique doit être unique car déjà utilisé !"
 * )
 */
class Rubrique
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(max=100, maxMessage="La rubrique ne doit pas dépasser {{ limit }} caractères!")
     * @Assert\NotBlank(message="Le titre de la rubrique ne doit pas être vide!")
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $title;

    /**
     * @Assert\Valid
     * @ORM\OneToMany(targetEntity="App\Entity\Categorie", mappedBy="rubrique")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="rubrique")
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $slug;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->users = new ArrayCollection();
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
     * @return Collection|Categorie[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setRubrique($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            // set the owning side to null (unless already changed)
            if ($category->getRubrique() === $this) {
                $category->setRubrique(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setRubrique($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getRubrique() === $this) {
                $user->setRubrique(null);
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
