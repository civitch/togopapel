<?php





namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class AnnonceSearch
{
    private ?string $title = null;

    private ?bool $type = null;

    /**
     * @Assert\LessThanOrEqual(propertyPath="priceMax")
     */
    private ?int $priceMin = null;

    /**
     * @Assert\GreaterThanOrEqual(propertyPath="priceMin")
     */
    private ?int $priceMax = null;


    private ?\App\Entity\Categorie $categorie = null;


    private ?\App\Entity\Ville $ville = null;

    private ?bool $particulier = null;

    private ?bool $profesional = null;



    /**
     * @return bool
     */
    public function isParticulier(): ?bool
    {
        return $this->particulier;
    }

    /**
     * @param bool $particulier
     * @return AnnonceSearch
     */
    public function setParticulier(?bool $particulier): ?self
    {
        $this->particulier = $particulier;
        return $this;
    }

    /**
     * @return bool
     */
    public function isProfesional(): ?bool
    {
        return $this->profesional;
    }

    /**
     * @param bool $profesional
     * @return AnnonceSearch
     */
    public function setProfesional(?bool $profesional): ?self
    {
        $this->profesional = $profesional;
        return $this;
    }


    /**
     * @return Ville|null
     */
    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    /**
     * @param Ville|null $ville
     * @return AnnonceSearch
     */
    public function setVille(?Ville $ville): AnnonceSearch
    {
        $this->ville = $ville;
        return $this;
    }


    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return AnnonceSearch
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getType(): ?bool
    {
        return $this->type;
    }

    /**
     * @param bool|null $type
     * @return AnnonceSearch
     */
    public function setType(?bool $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPriceMin(): ?int
    {
        return $this->priceMin;
    }

    /**
     * @param int|null $priceMin
     * @return AnnonceSearch
     */
    public function setPriceMin(?int $priceMin): self
    {
        $this->priceMin = $priceMin;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPriceMax(): ?int
    {
        return $this->priceMax;
    }

    /**
     * @param int|null $priceMax
     * @return AnnonceSearch
     */
    public function setPriceMax(?int $priceMax): self
    {
        $this->priceMax = $priceMax;
        return $this;
    }


    /**
     * @return Categorie|null
     */
    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    /**
     * @param Categorie|null $categorie
     * @return AnnonceSearch
     */
    public function setCategorie(?Categorie $categorie): self
    {

        $this->categorie = $categorie;
        return $this;
    }


}
