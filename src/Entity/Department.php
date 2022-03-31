<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(
 *     fields={"title", "role"},
 *     errorPath="role",
 *     message="Erreur lié au choix du départment car l'occurrence saisi exise déjà!",
 *     payload={"severity"="warning"}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\DepartmentRepository")
 */
class Department
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le titre ne doit pas dépasser {{ limit }} caractères"
     * )
     * @Assert\NotBlank(message="Le titre du département ne doit pas être vide")
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $title;

    /**
     * @Assert\Length(
     *      max = 100,
     *      maxMessage = "Le rôle ne doit pas dépasser {{ limit }} caractères"
     * )
     * @Assert\NotBlank(message="Le rôle du département ne doit pas être vide")
     * @Assert\Regex(
     *     pattern="/^ROLE_[A-Z]+_?([A-Z]+)?$/",
     *     message="Le rôle ne respecte pas structure demandée",
     *     payload={"severity"="error"},
     * )
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $role;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="department")
     */
    private $users;

    public function __construct()
    {
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

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
            $user->setDepartment($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getDepartment() === $this) {
                $user->setDepartment(null);
            }
        }

        return $this;
    }
}
