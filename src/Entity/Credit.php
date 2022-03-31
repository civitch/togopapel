<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CreditRepository")
 */
class Credit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $montant;

    /**
     * @ORM\Column(type="integer")
     */
    private $gdc;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DemandeCredit", mappedBy="credit", orphanRemoval=true)
     */
    private $demandeCredits;

    public function __construct()
    {
        $this->demandeCredits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getGdc(): ?int
    {
        return $this->gdc;
    }

    public function setGdc(int $gdc): self
    {
        $this->gdc = $gdc;

        return $this;
    }

    /**
     * @return Collection|DemandeCredit[]
     */
    public function getDemandeCredits(): Collection
    {
        return $this->demandeCredits;
    }

    public function addDemandeCredit(DemandeCredit $demandeCredit): self
    {
        if (!$this->demandeCredits->contains($demandeCredit)) {
            $this->demandeCredits[] = $demandeCredit;
            $demandeCredit->setCredit($this);
        }

        return $this;
    }

    public function removeDemandeCredit(DemandeCredit $demandeCredit): self
    {
        if ($this->demandeCredits->contains($demandeCredit)) {
            $this->demandeCredits->removeElement($demandeCredit);
            // set the owning side to null (unless already changed)
            if ($demandeCredit->getCredit() === $this) {
                $demandeCredit->setCredit(null);
            }
        }

        return $this;
    }
}
