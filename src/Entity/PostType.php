<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostTypeRepository")
 */
class PostType
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
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etiquette", inversedBy="postypes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etiquette;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $label;

    public function __construct()
    {
        $this->label = false;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getEtiquette(): ?Etiquette
    {
        return $this->etiquette;
    }

    public function setEtiquette(?Etiquette $etiquette): self
    {
        $this->etiquette = $etiquette;

        return $this;
    }

    public function getLabel(): ?bool
    {
        return $this->label;
    }

    public function setLabel(?bool $label): self
    {
        $this->label = $label;

        return $this;
    }
}
