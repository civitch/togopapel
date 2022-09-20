<?php

namespace App\Entity;

use App\Repository\PackRepository;
use App\Services\App\AppTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PackRepository::class)]
class Pack
{
    public \Doctrine\Common\Collections\ArrayCollection $users;
    use AppTrait;

    final const ROLES_PACK = [1 => 'star', 2 => 'vip', 3 => 'premium'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Assert\Length(min: 3, max: 50, minMessage: 'Le titre ne doit pas être inférieur à {{ limit }} caractères', maxMessage: 'Le titre ne doit pas être supérieur à {{ limit }} caractères')]
    #[Assert\NotBlank(message: 'Le titre ne doit pas être vide !')]
    #[ORM\Column(type: 'string', length: 50)]
    private $title;

    #[Assert\Length(max: 50, maxMessage: 'Le titre ne doit pas être supérieur à {{ limit }} caractères')]
    #[Assert\NotBlank(message: 'Le description ne doit pas être vide !')]
    #[ORM\Column(type: 'string', length: 255)]
    private $description;

    #[Assert\NotBlank(message: 'Le prix ne doit pas être vide !')]
    #[ORM\Column(type: 'integer')]
    private $price;

    #[Assert\NotBlank(message: 'La durée ne doit pas être vide !')]
    #[ORM\Column(type: 'integer')]
    private $duration;

    #[Assert\Range(min: 1, max: 3, minMessage: 'You must be at least {{ limit }} cm tall to enter', maxMessage: 'You cannot be taller than {{ limit }} cm to enter')]
    #[ORM\Column(type: 'integer', length: 1)]
    private $role;

    #[ORM\OneToMany(targetEntity: \App\Entity\UserPack::class, mappedBy: 'pack')]
    private $userPacks;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->userPacks = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRole(): ?int
    {
        return $this->role;
    }

    public function setRole(int $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getRoleType()
    {
        return self::ROLES_PACK[$this->role];
    }

    /**
     * @return Collection|UserPack[]
     */
    public function getUserPacks(): Collection
    {
        return $this->userPacks;
    }

    public function addUserPack(UserPack $userPack): self
    {
        if (!$this->userPacks->contains($userPack)) {
            $this->userPacks[] = $userPack;
            $userPack->setPack($this);
        }

        return $this;
    }

    public function removeUserPack(UserPack $userPack): self
    {
        if ($this->userPacks->contains($userPack)) {
            $this->userPacks->removeElement($userPack);
            // set the owning side to null (unless already changed)
            if ($userPack->getPack() === $this) {
                $userPack->setPack(null);
            }
        }

        return $this;
    }
}
