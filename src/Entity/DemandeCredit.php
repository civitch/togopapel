<?php

namespace App\Entity;


use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DemandeCreditRepository")
 */
class DemandeCredit
{
    const STATUS = [
        'waiting' => 0,
        'enabled' => 1,
        'disabled'=> 2
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @Assert\NotBlank(message="Veillez saisir une description")
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $image;

    /**
     * @Assert\Image(
     *     maxSize = "1024k",
     *     maxSizeMessage="La taille maximum autorisée est de {{ limit }} {{ suffix }}.",
     *     mimeTypes={"image/jpeg", "image/png"},
     *     mimeTypesMessage="Téléverser soit des images png ou jpeg"
     * )
     */
    private $pictureFile;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Credit", inversedBy="demandeCredits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $credit;

    /**
     * @Assert\Type(type="integer", message="Doit être défint et de type int")
     * @Assert\Range(
     *      min = 0,
     *      max = 2,
     *      notInRangeMessage = "La valeur doit être comprise entre {{ min }} et {{ max }}",
     * )
     * @ORM\Column(type="integer")
     */
    private $enabled;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->enabled = 0;
    }

    public function getPictureFile()
    {
        return $this->pictureFile;
    }


    public function setPictureFile($pictureFile): self
    {
        $picture = new Picture();
        $picture->setImageFile($pictureFile);
        $this->setImage($picture);
        $this->pictureFile = $pictureFile;
        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getImage(): ?Picture
    {
        return $this->image;
    }

    public function setImage(?Picture $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCredit(): ?Credit
    {
        return $this->credit;
    }

    public function setCredit(?Credit $credit): self
    {
        $this->credit = $credit;

        return $this;
    }

    public function getEnabled(): ?int
    {
        return $this->enabled;
    }

    public function setEnabled(?int $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
