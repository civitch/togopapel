<?php

namespace App\Entity;

use App\Repository\ParamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ParamRepository::class)]
#[Gedmo\SoftDeleteable(fieldName : "deletedAt", timeAware :false, hardDelete : true)]
#[Gedmo\Loggable]
class Param
{

    use SoftDeleteableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private $id;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Gedmo\Versioned]
    private ?string $value = null;

    #[ORM\Column(length: 255)]
    #[Gedmo\Versioned]
    #[Assert\NotBlank(groups: ['personnePhysique'])]
    #[Assert\NotNull(groups: ['personnePhysique', 'identite'])]
    private ?string $label = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeData = "string";

    #[ORM\Column(nullable: true)]
    #[Gedmo\Versioned]
    private ?int $status = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Gedmo\Versioned]
    private ?string $description = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getTypeData(): ?string
    {
        return $this->typeData;
    }

    public function setTypeData(?string $typeData): self
    {
        $this->typeData = $typeData;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

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
}
