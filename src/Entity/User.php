<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email', errorPath: 'email', message: 'Cette adresse mail est déjà utilisé')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Assert\NotBlank(message: "L'email doit être saisi")]
    #[Assert\Email(message: "L'adresse mail {{ value }} n'est pas valide")]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\Regex(pattern: '/^(?=.*\d{1,})(?=.*\w).{8,}$/', message: 'Le mot de passe ne correspond pas à la structure demandée')]
    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[Assert\NotBlank(message: 'La date de création du compte ne doit pas rester vide!')]
    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastLogin;

    #[Assert\Type(type: 'bool', message: "La valeur {{ value }} n'est pas un {{ type }}.")]
    #[ORM\Column(type: 'boolean')]
    private bool $enabled = false;

    #[Assert\Choice(['madame', 'monsieur'])]
    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $civility;

    #[Assert\Length(max: 255, maxMessage: "L'adresse ne doit pas excéder {{ limit }} caractères")]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $adresse;

    #[Assert\Length(max: 100, maxMessage: 'Le n° de tél doit pas excéder {{ limit }} caractères')]
    #[Assert\Regex(pattern: '/^[0-9 -.]+$/', message: 'Le numéro de téléphone ne respecte pas les règles')]
    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $tel;

    #[Assert\Length(max: 255, maxMessage: 'Ne doit pas excéder {{ limit }} caractères')]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $confirmationToken;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $confirmationAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $resetToken;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $resetAt;

    #[Assert\Length(max: 255, maxMessage: 'Le nom ne doit pas excéder {{ limit }} caractères')]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $name;

    #[Assert\Length(max: 255, maxMessage: 'le prénom ne doit pas excéder {{ limit }} caractères')]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $firstname;

    #[Assert\Length(max: 255, maxMessage: 'Ne doit pas excéder {{ limit }} caractères')]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $society;

    #[Assert\Length(max: 255, maxMessage: 'Ne doit pas excéder {{ limit }} caractères')]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $siren;

    #[Assert\Valid]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Department::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private $department;

    #[Assert\Valid]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Rubrique::class, inversedBy: 'users')]
    private $rubrique;

    #[Assert\Valid]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Ville::class)]
    private $ville;

    #[ORM\OneToMany(targetEntity: \App\Entity\Annonce::class, mappedBy: 'user')]
    private $annonces;

    #[ORM\JoinTable(name: 'user_favoris')]
    #[ORM\ManyToMany(targetEntity: \App\Entity\Annonce::class, inversedBy: 'favoris')]
    private $favoris;


    #[ORM\OneToMany(targetEntity: \App\Entity\UserPack::class, mappedBy: 'user')]
    private $userPacks;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $wallet;

    #[ORM\OneToMany(targetEntity: \App\Entity\NotificationUser::class, mappedBy: 'user')]
    private $notificationUsers;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\ManyToOne(targetEntity: \App\Entity\IndicatifPays::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'indicatif_pays_id', referencedColumnName: 'id')]
    private $indicatifPays;




    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->annonces = new ArrayCollection();
        $this->favoris = new ArrayCollection();
        $this->userPacks = new ArrayCollection();
        $this->notificationUsers = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function addRole($role)
    {
        if (!$role) {
            return $this;
        }
        $role = strtoupper((string) $role);
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper((string) $role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
        return $this;
    }

    public function hasRole($role)
    {
        return in_array(strtoupper((string) $role), $this->roles, true);
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getCivility(): ?string
    {
        return $this->civility;
    }

    public function setCivility(?string $civility): self
    {
        $this->civility = $civility;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getConfirmationAt(): ?\DateTimeInterface
    {
        return $this->confirmationAt;
    }

    public function setConfirmationAt(\DateTimeInterface $confirmationAt): self
    {
        $this->confirmationAt = $confirmationAt;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getResetAt(): ?\DateTimeInterface
    {
        return $this->resetAt;
    }

    public function setResetAt(?\DateTimeInterface $resetAt): self
    {
        $this->resetAt = $resetAt;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getSociety(): ?string
    {
        return $this->society;
    }

    public function setSociety(?string $society): self
    {
        $this->society = $society;

        return $this;
    }

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(?string $siren): self
    {
        $this->siren = $siren;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getRubrique(): ?Rubrique
    {
        return $this->rubrique;
    }

    public function setRubrique(?Rubrique $rubrique): self
    {
        $this->rubrique = $rubrique;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

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
            $annonce->setUser($this);
        }

        return $this;
    }

    public function removeAnnonce(Annonce $annonce): self
    {
        if ($this->annonces->contains($annonce)) {
            $this->annonces->removeElement($annonce);
            // set the owning side to null (unless already changed)
            if ($annonce->getUser() === $this) {
                $annonce->setUser(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|Annonce[]
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Annonce $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris[] = $favori;
        }

        return $this;
    }

    public function removeFavori(Annonce $favori): self
    {
        if ($this->favoris->contains($favori)) {
            $this->favoris->removeElement($favori);
        }

        return $this;
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
            $userPack->setUser($this);
        }

        return $this;
    }

    public function removeUserPack(UserPack $userPack): self
    {
        if ($this->userPacks->contains($userPack)) {
            $this->userPacks->removeElement($userPack);
            // set the owning side to null (unless already changed)
            if ($userPack->getUser() === $this) {
                $userPack->setUser(null);
            }
        }

        return $this;
    }


    public function hasPack(Pack $pack)
    {
        foreach ($this->userPacks as $userPack){
            /** @var UserPack $userPack */
            if($userPack->isPack($pack)){
                return true;
            }
        }
        return false;
    }

    public function getWallet(): ?int
    {
        return $this->wallet;
    }

    public function setWallet(?int $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * @return Collection|NotificationUser[]
     */
    public function getNotificationUsers(): Collection
    {
        return $this->notificationUsers;
    }

    public function addNotificationUser(NotificationUser $notificationUser): self
    {
        if (!$this->notificationUsers->contains($notificationUser)) {
            $this->notificationUsers[] = $notificationUser;
            $notificationUser->setUser($this);
        }

        return $this;
    }

    public function removeNotificationUser(NotificationUser $notificationUser): self
    {
        if ($this->notificationUsers->contains($notificationUser)) {
            $this->notificationUsers->removeElement($notificationUser);
            // set the owning side to null (unless already changed)
            if ($notificationUser->getUser() === $this) {
                $notificationUser->setUser(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

public function getIndicatifPays(): ?IndicatifPays
{
    return $this->indicatifPays;
}

public function setIndicatifPays(?IndicatifPays $indicatifPays): self
{
    $this->indicatifPays = $indicatifPays;

    return $this;
}





}
