<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
class Annonce
{
    final const TYPE = [
        true  => 'offre',
        false => 'demande'
    ];

    final const STATE = [
        'new'     => 'neuf',
        'very'    => 'très bon état',
        'good'    => 'bon état',
        'satisfy' => 'satisfaisant'
    ];

    final const STATUS = [
        'waiting' => 0,
        'enabled' => 1,
        'disabled'=> 2
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $nbrVue=0;

    #[Assert\NotBlank(message: "Le titre de l'anonce ne doit pas rester vide")]
    #[Assert\Length(min: 3, max: 150, minMessage: 'Le titre de votre annonce doit avoir au moins {{ limit }} caractères', maxMessage: 'Le titre de votre annonce doit pas accéder plus de {{ limit }} caractères')]
    #[ORM\Column(type: 'string', length: 150)]
    private $title;

    #[Assert\Type('bool', message: "La valeur {{ value }} n'est pas celle attendue.")]
    #[ORM\Column(type: 'boolean')]
    private $type;

    #[Assert\Length(min: 3, max: 10, minMessage: 'La valeur minimum doit être de {{ limit }} caractères', maxMessage: 'La valeur maximum doit être de {{ limit }} caractères')]
    #[Assert\Choice(['new', 'very', 'good', 'satisfy'], message: 'Choisir un état valide')]
    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private $state;

    #[Assert\Length(min: 10, minMessage: "La description de l'annonce doit avoir au moins {{ limit }} caractères")]
    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[Assert\Positive(message: 'Cette valeur doit etre positive')]
    #[Assert\Type(type: 'float', message: "La valeur {{ value }} n'est pas valide!")]
    #[ORM\Column(type: 'float', scale: 2, nullable: true)]
    private $price;

    #[Assert\NotBlank(message: "La catégorie de l'annonce ne doit pas rester vide!")]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Categorie::class, inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    private $categorie;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class, inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;


    #[Assert\Image(mimeTypes: ['image/jpeg', 'image/png'], mimeTypesMessage: 'Téléverser soit des images png ou jpeg')]
    private $pictureFileOne;

    #[Assert\Image(mimeTypes: ['image/jpeg', 'image/png'], mimeTypesMessage: 'Téléverser soit des images png ou jpeg')]
    private $pictureFileTwo;


    #[Assert\Image(mimeTypes: ['image/jpeg', 'image/png'], mimeTypesMessage: 'Téléverser soit des images png ou jpeg')]
    private $pictureFileThree;


    #[Assert\Image(mimeTypes: ['image/jpeg', 'image/png'], mimeTypesMessage: 'Téléverser soit des images png ou jpeg')]
    private $pictureFileFour;



    #[Assert\NotBlank(message: "La date de création de l'annocne doit être mentionnée")]
    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[Assert\Count(max: 7, maxMessage: 'La limite de téléchargement de fichiers est de {{ limit }}')]
    #[ORM\OneToMany(targetEntity: \App\Entity\Picture::class, mappedBy: 'annonce', orphanRemoval: true, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: true)]
    private $pictures;


    private ?\App\Entity\Picture $picture = null;

    #[Assert\Type(type: 'integer', message: 'Doit être défint et de type int')]
    #[Assert\Range(min: 0, max: 2, notInRangeMessage: 'La valeur doit être comprise entre {{ min }} et {{ max }}')]
    #[ORM\Column(type: 'integer')]
    private $enabled;

    #[Assert\Type(type: 'float', message: 'doit être de type float')]
    #[Assert\NotBlank(message: 'Le lieu définit ne correspond pas à une région du Togo')]
    #[ORM\Column(type: 'float', scale: 4, precision: 6)]
    private $lat;

    #[Assert\Type(type: 'float', message: 'doit être de type float')]
    #[Assert\NotBlank(message: 'Le lieu définit ne correspond pas à une région du Togo')]
    #[ORM\Column(type: 'float', scale: 4, precision: 7)]
    private $lng;

    #[Assert\Length(max: 255, maxMessage: "L'adresse doit contenir au maximum {{ limit }} caractères")]
    #[Assert\NotBlank(message: 'Veuiller définir une adresse pour votre annonce')]
    #[ORM\Column(type: 'string', length: 255)]
    private $adresse;

    #[Assert\NotBlank(message: 'Veuillez saisir les informations sur la ville')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Ville::class, inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    private $ville;

    #[ORM\ManyToMany(targetEntity: \App\Entity\User::class, mappedBy: 'favoris')]
    private $favoris;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $packStar;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $packVip;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $packPremium;

    #[ORM\Column(type: 'boolean')]
    private bool $hideTel = false;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;


    public function __construct()
    {
        $this->enabled = self::STATUS['waiting'];
        $this->createdAt = new DateTime();
        $this->pictures = new ArrayCollection();
        $this->favoris = new ArrayCollection();
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

    public function getType(): ?bool
    {
        return $this->type;
    }

    public function setType(bool $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getState(): ?string
    {
        return self::STATE[$this->state] ?? null;
    }



    public function getStateForm(string $value): string
    {
        $state = array_keys(self::STATE, $value);
        return $state[0];
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getPriceFormat(): string
    {
        return number_format($this->price, 0, ',', ' ');
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
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


    /**
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function getPicture(): ?Picture
    {
        /*if($this->pictures->isEmpty()){
            return null;
        }
        return $this->pictures->first();*/
        return $this->picture;
    }

    public function setPicture(Picture $picture): self
    {
        $this->picture = $picture;
        return $this;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setAnnonce($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
            // set the owning side to null (unless already changed)
            if ($picture->getAnnonce() === $this) {
                $picture->setAnnonce(null);
            }
        }

        return $this;
    }


    private function addPictureFile($pictureFile)
    {
        $picture = new Picture();
        $picture->setImageFile($pictureFile);
        $this->addPicture($picture);
    }

    /**
     * @return mixed
     */
    public function getPictureFileOne()
    {
        return $this->pictureFileOne;
    }

    public function setPictureFileOne(mixed $pictureFile): self
    {
        $this->addPictureFile($pictureFile);
        $this->pictureFileOne = $pictureFile;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getPictureFileTwo()
    {
        return $this->pictureFileTwo;
    }

    public function setPictureFileTwo(mixed $pictureFile): self
    {
        $this->addPictureFile($pictureFile);
        $this->pictureFileTwo = $pictureFile;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getPictureFileThree()
    {
        return $this->pictureFileThree;
    }

    public function setPictureFileThree(mixed $pictureFile): self
    {
        $this->addPictureFile($pictureFile);
        $this->pictureFileThree = $pictureFile;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getPictureFileFour()
    {
        return $this->pictureFileFour;
    }

    public function setPictureFileFour(mixed $pictureFile): self
    {
        $this->addPictureFile($pictureFile);
        $this->pictureFileFour = $pictureFile;
        return $this;
    }


    public function getEnabled(): ?int
    {
        return $this->enabled;
    }

    public function setEnabled(int $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

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
     * @param $payload
     */
    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if($this->getType() && empty($this->getPrice())){
            $context->buildViolation('Pour une offre le prix doit etre définit ')
                ->atPath('price')
                ->addViolation()
            ;
        }


    }


    /**
     * @return Collection|User[]
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(User $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris[] = $favori;
            $favori->addFavori($this);
        }

        return $this;
    }

    public function removeFavori(User $favori): self
    {
        if ($this->favoris->contains($favori)) {
            $this->favoris->removeElement($favori);
            $favori->removeFavori($this);
        }

        return $this;
    }

    public function getPackStar(): ?bool
    {
        return $this->packStar;
    }

    public function setPackStar(?bool $packStar): self
    {
        $this->packStar = $packStar;

        return $this;
    }

    public function getPackVip(): ?bool
    {
        return $this->packVip;
    }

    public function setPackVip(?bool $packVip): self
    {
        $this->packVip = $packVip;

        return $this;
    }

    public function getPackPremium(): ?bool
    {
        return $this->packPremium;
    }

    public function setPackPremium(?bool $packPremium): self
    {
        $this->packPremium = $packPremium;

        return $this;
    }

    public function getHideTel(): ?bool
    {
        return $this->hideTel;
    }

    public function setHideTel(bool $hideTel): self
    {
        $this->hideTel = $hideTel;

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

    public function getNbrVue(): int
    {
        return $this->nbrVue;
    }

    public function setNbrVue(int $nbrVue): void
    {
        $this->nbrVue = $nbrVue;
    }



}
